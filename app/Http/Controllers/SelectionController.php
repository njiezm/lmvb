<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use App\Models\Team;

class SelectionController extends Controller
{
    public function index()
    {
        $seniorTeams = Team::active()->senior()->withCount('players')->get();
        $youthTeams = Team::active()->youth()->withCount('players')->get();
        $videos = Gallery::active()->where('category', 'selection')->where('type', 'video')->latest()->take(2)->get();

        return view('selections.index', compact('seniorTeams', 'youthTeams', 'videos'));
    }

    public function show(Team $team)
    {
        abort_unless($team->active, 404);
        $team->load('players');

        return view('selections.show', compact('team'));
    }
}
