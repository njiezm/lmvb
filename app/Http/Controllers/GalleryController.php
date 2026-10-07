<?php

namespace App\Http\Controllers;

use App\Models\Gallery;

class GalleryController extends Controller
{
    public function index()
    {
        $items = Gallery::active()->with('club')->latest()->paginate(24);

        return view('gallery.index', ['items' => $items, 'categories' => $this->categories(), 'category' => null]);
    }

    public function category(string $category)
    {
        abort_unless(array_key_exists($category, Gallery::CATEGORIES), 404);
        $items = Gallery::active()->with('club')->where('category', $category)->latest()->paginate(24);

        return view('gallery.index', ['items' => $items, 'categories' => $this->categories(), 'category' => $category]);
    }

    public function show(Gallery $gallery)
    {
        abort_unless($gallery->active, 404);

        $related = Gallery::active()
            ->where('category', $gallery->category)
            ->where('id', '!=', $gallery->id)
            ->latest()->take(6)->get();

        return view('gallery.show', ['item' => $gallery, 'related' => $related]);
    }

    private function categories()
    {
        return Gallery::active()->selectRaw('category, count(*) as total')->groupBy('category')->pluck('total', 'category');
    }
}
