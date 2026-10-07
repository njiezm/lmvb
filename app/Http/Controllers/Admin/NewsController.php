<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Club;
use App\Models\News;
use App\Services\ImageUploader;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class NewsController extends Controller
{
    public function __construct(private ImageUploader $images) {}

    public function index(Request $request)
    {
        $user = $request->user();
        $news = News::with(['category', 'user', 'club'])
            ->when(! $user->isSuperAdmin(), fn ($q) => $q->where('club_id', $user->club_id))
            ->when($request->query('q'), fn ($q, $s) => $q->whereLike('title', "%$s%"))
            ->when($request->query('status'), fn ($q, $s) => $q->where('status', $s))
            ->latest()
            ->paginate(15)->withQueryString();

        return view('admin.news.index', compact('news'));
    }

    public function create()
    {
        return view('admin.news.form', ['news' => new News(['status' => 'draft']), ...$this->formData()]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['slug'] = $this->uniqueSlug($data['title']);
        $data['user_id'] = $request->user()->id;
        $data['image'] = $request->hasFile('image') ? $this->images->store($request->file('image'), 'news') : null;
        $data['published_at'] = $this->publishedAt($data, null);

        News::create($data);

        return redirect()->route('admin.news.index')->with('success', 'Actualité créée.');
    }

    public function edit(Request $request, News $news)
    {
        $this->authorizeNews($request, $news);

        return view('admin.news.form', ['news' => $news, ...$this->formData()]);
    }

    public function update(Request $request, News $news)
    {
        $this->authorizeNews($request, $news);

        $data = $this->validated($request, $news);
        if ($data['title'] !== $news->title) {
            $data['slug'] = $this->uniqueSlug($data['title'], $news->id);
        }
        $data['image'] = $this->images->replace($request->file('image'), $news->image, 'news');
        if ($request->boolean('remove_image')) {
            $this->images->delete($news->image);
            $data['image'] = null;
        }
        $data['published_at'] = $this->publishedAt($data, $news);

        $news->update($data);

        return redirect()->route('admin.news.index')->with('success', 'Actualité mise à jour.');
    }

    public function destroy(Request $request, News $news)
    {
        $this->authorizeNews($request, $news);
        $this->images->delete($news->image);
        $news->delete();

        return redirect()->route('admin.news.index')->with('success', 'Actualité supprimée.');
    }

    private function validated(Request $request, ?News $news = null): array
    {
        $user = $request->user();

        $data = $request->validate([
            'title' => 'required|string|max:200',
            'excerpt' => 'required|string|max:400',
            'content' => 'required|string|max:100000',
            'category_id' => 'required|exists:categories,id',
            'club_id' => ['nullable', Rule::exists('clubs', 'id')],
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:6144',
            'image_credit' => 'nullable|string|max:150',
            'source_url' => 'nullable|url|max:255',
            'status' => 'required|in:draft,published,archived',
            'published_at' => 'nullable|date',
            'featured' => 'nullable|boolean',
        ]);

        $data['content'] = safe_html($data['content']);
        $data['featured'] = $user->isSuperAdmin() && $request->boolean('featured');
        // Un admin de club publie toujours au nom de son club.
        if (! $user->isSuperAdmin()) {
            $data['club_id'] = $user->club_id;
        }
        unset($data['image']);

        return $data;
    }

    private function publishedAt(array $data, ?News $news)
    {
        if (! empty($data['published_at'])) {
            return $data['published_at'];
        }

        return $data['status'] === 'published' ? ($news?->published_at ?? now()) : $news?->published_at;
    }

    private function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title) ?: 'actualite';
        $slug = $base;
        $i = 2;
        while (News::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }

    private function authorizeNews(Request $request, News $news): void
    {
        abort_unless($request->user()->managesClub($news->club_id), 403);
    }

    private function formData(): array
    {
        return [
            'categories' => Category::orderBy('name')->get(),
            'clubs' => Club::active()->orderBy('name')->get(['id', 'name']),
        ];
    }
}
