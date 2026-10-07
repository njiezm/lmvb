<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Player;
use App\Models\Team;
use App\Services\ImageUploader;
use Illuminate\Http\Request;

/** Joueurs / joueuses d'une sélection. */
class PlayerController extends Controller
{
    public function __construct(private ImageUploader $images) {}

    public function create(Team $team)
    {
        return view('admin.players.form', ['team' => $team, 'player' => new Player]);
    }

    public function store(Request $request, Team $team)
    {
        $data = $this->validated($request);
        $data['photo'] = $request->hasFile('photo') ? $this->images->store($request->file('photo'), 'players', 800) : null;

        $team->players()->create($data);

        return redirect()->route('admin.teams.edit', $team)->with('success', 'Joueur ajouté.');
    }

    public function edit(Player $player)
    {
        return view('admin.players.form', ['team' => $player->team, 'player' => $player]);
    }

    public function update(Request $request, Player $player)
    {
        $data = $this->validated($request);
        $data['photo'] = $this->images->replace($request->file('photo'), $player->photo, 'players', 800);

        $player->update($data);

        return redirect()->route('admin.teams.edit', $player->team)->with('success', 'Joueur mis à jour.');
    }

    public function destroy(Player $player)
    {
        $team = $player->team;
        $this->images->delete($player->photo);
        $player->delete();

        return redirect()->route('admin.teams.edit', $team)->with('success', 'Joueur retiré de la sélection.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:80',
            'last_name' => 'required|string|max:80',
            'position' => 'required|string|max:60',
            'number' => 'nullable|integer|min:0|max:99',
            'height' => 'nullable|integer|min:120|max:240',
            'birth_date' => 'nullable|date|before:today',
            'club_name' => 'nullable|string|max:120',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
        ]);
        unset($data['photo']);

        return $data;
    }
}
