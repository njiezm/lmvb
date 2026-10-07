<?php

namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\Game;
use App\Models\Season;
use App\Models\Standing;

class ClubController extends Controller
{
    public function index()
    {
        $clubs = Club::active()->orderBy('name')->get();

        return view('clubs.index', compact('clubs'));
    }

    public function show(Club $club)
    {
        abort_unless($club->active || auth()->user()?->canAccessAdmin(), 404);

        $upcoming = Game::scheduled()->forClub($club->id)->withDisplay()->orderBy('date_time')->take(6)->get();
        $results = Game::finished()->forClub($club->id)->whereNotNull('date_time')->withDisplay()->orderByDesc('date_time')->take(8)->get();

        $season = Season::current();
        $standings = Standing::where('club_id', $club->id)
            ->whereHas('competition', fn ($q) => $q->active()->when($season, fn ($q) => $q->where('season_id', $season->id)))
            ->with('competition.season')
            ->get()
            ->sortBy(fn ($s) => $s->competition->sort_order);

        $news = $club->news()->published()->newest()->take(3)->get();
        $photos = $club->photos()->active()->latest()->take(8)->get();

        $record = Game::finished()->forClub($club->id)->get(['home_team_id', 'away_team_id', 'home_score', 'away_score']);
        $wins = $record->filter(fn ($g) => ($g->home_team_id === $club->id) === ($g->home_score > $g->away_score))->count();

        return view('clubs.show', compact('club', 'upcoming', 'results', 'standings', 'news', 'photos', 'season') + [
            'stats' => ['played' => $record->count(), 'wins' => $wins],
        ]);
    }
}
