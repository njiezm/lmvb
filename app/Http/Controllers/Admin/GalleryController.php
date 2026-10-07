<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\Gallery;
use App\Services\ImageUploader;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class GalleryController extends Controller
{
    public function __construct(private ImageUploader $images) {}

    public function index(Request $request)
    {
        $user = $request->user();
        $items = Gallery::with('club')
            ->when(! $user->isSuperAdmin(), fn ($q) => $q->where('club_id', $user->club_id))
            ->when($request->query('category'), fn ($q, $c) => $q->where('category', $c))
            ->when($request->query('type'), fn ($q, $t) => $q->where('type', $t))
            ->when($request->query('q'), fn ($q, $s) => $q->whereLike('title', "%$s%"))
            ->latest()
            ->paginate(24)->withQueryString();

        return view('admin.gallery.index', compact('items'));
    }

    public function create()
    {
        return view('admin.gallery.form', ['item' => new Gallery(['active' => true, 'type' => 'image', 'category' => 'match']), 'clubs' => $this->clubs()]);
    }

    /** Ajout en lot : plusieurs photos et/ou une vidéo YouTube. */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:200',
            'type' => 'required|in:image,video',
            'images' => 'required_if:type,image|array|max:30',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:8192',
            'video_url' => ['required_if:type,video', 'nullable', 'url', 'regex:~(youtube\.com|youtu\.be)~'],
            'category' => ['required', Rule::in(array_keys(Gallery::CATEGORIES))],
            'club_id' => ['nullable', Rule::exists('clubs', 'id')],
            'description' => 'nullable|string|max:1000',
            'credit' => 'nullable|string|max:150',
            'active' => 'nullable|boolean',
        ], ['video_url.regex' => 'Seuls les liens YouTube sont acceptés.']);

        $base = [
            'title' => $data['title'],
            'type' => $data['type'],
            'category' => $data['category'],
            'club_id' => $this->clubId($request, $data['club_id'] ?? null),
            'description' => $data['description'] ?? null,
            'credit' => $data['credit'] ?? null,
            'active' => $request->boolean('active', true),
        ];

        if ($data['type'] === 'video') {
            Gallery::create($base + ['video_url' => $data['video_url']]);
            $count = 1;
        } else {
            foreach ($request->file('images') as $file) {
                Gallery::create($base + ['image' => $this->images->store($file, 'gallery', 1920)]);
            }
            $count = count($request->file('images'));
        }

        return redirect()->route('admin.gallery.index')->with('success', "$count élément(s) ajouté(s) à la galerie.");
    }

    public function edit(Request $request, Gallery $gallery)
    {
        $this->authorizeItem($request, $gallery);

        return view('admin.gallery.form', ['item' => $gallery, 'clubs' => $this->clubs()]);
    }

    public function update(Request $request, Gallery $gallery)
    {
        $this->authorizeItem($request, $gallery);

        $data = $request->validate([
            'title' => 'required|string|max:200',
            'video_url' => ['nullable', 'url', 'regex:~(youtube\.com|youtu\.be)~'],
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:8192',
            'category' => ['required', Rule::in(array_keys(Gallery::CATEGORIES))],
            'club_id' => ['nullable', Rule::exists('clubs', 'id')],
            'description' => 'nullable|string|max:1000',
            'credit' => 'nullable|string|max:150',
        ]);

        $data['club_id'] = $this->clubId($request, $data['club_id'] ?? null);
        $data['active'] = $request->boolean('active');
        $data['image'] = $this->images->replace($request->file('image'), $gallery->image, 'gallery', 1920);

        $gallery->update($data);

        return redirect()->route('admin.gallery.index')->with('success', 'Élément mis à jour.');
    }

    public function destroy(Request $request, Gallery $gallery)
    {
        $this->authorizeItem($request, $gallery);
        $this->images->delete($gallery->image);
        $gallery->delete();

        return back()->with('success', 'Élément supprimé.');
    }

    private function clubId(Request $request, $requested): ?int
    {
        return $request->user()->isSuperAdmin() ? ($requested ?: null) : $request->user()->club_id;
    }

    private function authorizeItem(Request $request, Gallery $gallery): void
    {
        abort_unless($request->user()->managesClub($gallery->club_id), 403);
    }

    private function clubs()
    {
        return Club::active()->orderBy('name')->get(['id', 'name']);
    }
}
