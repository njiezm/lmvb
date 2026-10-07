<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\News;

class NewsController extends Controller
{
    public function index()
    {
        $news = News::published()->with(['category', 'club'])->newest()->paginate(12);
        $categories = Category::withCount(['news' => fn ($q) => $q->published()])->orderBy('name')->get();

        return view('news.index', compact('news', 'categories'));
    }

    public function show(string $slug)
    {
        $news = News::where('slug', $slug)->published()->with(['category', 'club', 'user'])->firstOrFail();
        $news->increment('views');

        $relatedNews = News::published()
            ->where('id', '!=', $news->id)
            ->where('category_id', $news->category_id)
            ->with('category')
            ->newest()->take(3)->get();

        return view('news.show', compact('news', 'relatedNews'));
    }

    public function category(Category $category)
    {
        $news = $category->news()->published()->with(['category', 'club'])->newest()->paginate(12);
        $categories = Category::withCount(['news' => fn ($q) => $q->published()])->orderBy('name')->get();

        return view('news.index', compact('news', 'categories', 'category'));
    }
}
