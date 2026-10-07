<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Services\ImageUploader;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ClubController extends Controller
{
    public function __construct(private ImageUploader $images) {}

    public function index(Request $request)
    {
        $user = $request->user();
        if (! $user->isSuperAdmin()) {
            return redirect()->route('admin.clubs.edit', $user->club);
        }

        $clubs = Club::query()
            ->withCount(['homeGames', 'awayGames', 'admins'])
            ->when($request->query('q'), fn ($q, $s) => $q->where(fn ($q) => $q->whereLike('name', "%$s%")->orWhereLike('city', "%$s%")->orWhere('ffvb_number', 'like', "%$s%")))
            ->when($request->query('status') !== null && $request->query('status') !== '', fn ($q) => $q->where('active', $request->boolean('status')))
            ->orderBy($request->query('sort') === 'members' ? 'members_count' : 'name', $request->query('sort') === 'members' ? 'desc' : 'asc')
            ->paginate(20)->withQueryString();

        return view('admin.clubs.index', compact('clubs'));
    }

    public function create(Request $request)
    {
        abort_unless($request->user()->isSuperAdmin(), 403);

        return view('admin.clubs.form', ['club' => new Club(['active' => true, 'color' => '#242868'])]);
    }

    public function store(Request $request)
    {
        abort_unless($request->user()->isSuperAdmin(), 403);

        $data = $this->validated($request);
        $data['slug'] = $this->uniqueSlug($data['name']);
        $data['logo'] = $request->hasFile('logo') ? $this->images->store($request->file('logo'), 'clubs', 600) : null;

        Club::create($data);

        return redirect()->route('admin.clubs.index')->with('success', 'Club créé.');
    }

    public function edit(Request $request, Club $club)
    {
        abort_unless($request->user()->managesClub($club->id), 403);

        return view('admin.clubs.form', compact('club'));
    }

    public function update(Request $request, Club $club)
    {
        abort_unless($request->user()->managesClub($club->id), 403);

        $data = $this->validated($request, $club);
        $data['logo'] = $this->images->replace($request->file('logo'), $club->logo, 'clubs', 600);
        if ($request->boolean('remove_logo')) {
            $this->images->delete($club->logo);
            $data['logo'] = null;
        }

        $club->update($data);

        return $request->user()->isSuperAdmin()
            ? redirect()->route('admin.clubs.index')->with('success', 'Club mis à jour.')
            : back()->with('success', 'Fiche du club mise à jour.');
    }

    /** Suppression « douce » : l'historique des matchs est conservé. */
    public function destroy(Request $request, Club $club)
    {
        abort_unless($request->user()->isSuperAdmin(), 403);
        $club->delete();

        return redirect()->route('admin.clubs.index')->with('success', 'Club archivé (ses matchs restent visibles).');
    }

    private function validated(Request $request, ?Club $club = null): array
    {
        $super = $request->user()->isSuperAdmin();

        $data = $request->validate([
            'name' => 'required|string|max:150',
            'short_name' => 'nullable|string|max:40',
            'ffvb_number' => ['nullable', 'string', 'max:12', Rule::unique('clubs')->ignore($club)],
            'description' => 'nullable|string|max:5000',
            'city' => 'nullable|string|max:100',
            'venue' => 'nullable|string|max:150',
            'address' => 'nullable|string|max:255',
            'phone' => ['nullable', 'string', 'max:20', 'regex:/^[0-9 +().-]{8,20}$/'],
            'email' => 'nullable|email|max:150',
            'website' => 'nullable|url|max:255',
            'facebook' => 'nullable|url|max:255',
            'instagram' => 'nullable|url|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'members_count' => 'nullable|integer|min:0|max:100000',
            'founded_year' => 'nullable|integer|min:1900|max:'.date('Y'),
        ]);

        $data['members_count'] = $data['members_count'] ?? 0;
        unset($data['logo']);

        // Champs réservés au super admin.
        if ($super) {
            $data['active'] = $request->boolean('active');
        } else {
            unset($data['ffvb_number']);
        }

        return $data;
    }

    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name) ?: 'club';
        $slug = $base;
        $i = 2;
        while (Club::withTrashed()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }
}
