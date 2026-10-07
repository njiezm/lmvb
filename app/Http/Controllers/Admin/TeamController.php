<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Services\ImageUploader;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/** Sélections régionales. */
class TeamController extends Controller
{
    public function __construct(private ImageUploader $images) {}

    public function index()
    {
        $teams = Team::withCount('players')->with('players')->orderBy('category')->get();

        return view('admin.teams.index', compact('teams'));
    }

    public function create()
    {
        return view('admin.teams.form', ['team' => new Team(['active' => true])]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['slug'] = $this->uniqueSlug($data['name']);
        $data['photo'] = $request->hasFile('photo') ? $this->images->store($request->file('photo'), 'teams') : null;

        $team = Team::create($data);

        return redirect()->route('admin.teams.edit', $team)->with('success', 'Sélection créée : ajoutez maintenant les joueurs.');
    }

    public function edit(Team $team)
    {
        $team->load('players');

        return view('admin.teams.form', compact('team'));
    }

    public function update(Request $request, Team $team)
    {
        $data = $this->validated($request);
        if ($data['name'] !== $team->name) {
            $data['slug'] = $this->uniqueSlug($data['name'], $team->id);
        }
        $data['photo'] = $this->images->replace($request->file('photo'), $team->photo, 'teams');

        $team->update($data);

        return redirect()->route('admin.teams.index')->with('success', 'Sélection mise à jour.');
    }

    public function destroy(Team $team)
    {
        $this->images->delete($team->photo);
        $team->players->each(fn ($p) => $this->images->delete($p->photo));
        $team->delete();

        return redirect()->route('admin.teams.index')->with('success', 'Sélection supprimée.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'category' => ['required', Rule::in(array_keys(Team::CATEGORIES))],
            'coach' => 'nullable|string|max:150',
            'description' => 'nullable|string|max:5000',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:6144',
        ]);
        $data['active'] = $request->boolean('active');
        unset($data['photo']);

        return $data;
    }

    private function uniqueSlug(string $name, ?int $ignore = null): string
    {
        $base = Str::slug($name) ?: 'selection';
        $slug = $base;
        $i = 2;
        while (Team::where('slug', $slug)->when($ignore, fn ($q) => $q->where('id', '!=', $ignore))->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }
}
