<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use App\Models\Season;
use Illuminate\Http\Request;

class CompetitionController extends Controller
{
    public function index(Request $request)
    {
        $seasons = Season::whereHas('competitions')->orderByDesc('name')->get();
        $season = $seasons->firstWhere('slug', $request->query('saison')) ?? Season::current() ?? $seasons->first();

        $competitions = $season
            ? $season->competitions()->active()->withCount(['games', 'games as finished_count' => fn ($q) => $q->finished()])->get()
            : collect();

        return view('competitions.index', [
            'seasons' => $seasons,
            'season' => $season,
            'groups' => $competitions->groupBy('category')->sortBy(fn ($c, $key) => array_search($key, array_keys(Competition::CATEGORIES))),
        ]);
    }

    public function show(Season $season, Competition $competition)
    {
        abort_unless($competition->active, 404);

        $competition->load(['standings.club']);
        $games = $competition->games()->withDisplay()
            ->orderByRaw('matchday is null, matchday')
            ->orderBy('date_time')
            ->get();

        $byMatchday = $games->groupBy(fn ($g) => $g->matchday ?? 0);

        // Journée à afficher par défaut : la première qui contient un match non joué.
        $currentDay = optional($games->first(fn ($g) => $g->status !== 'finished' && $g->date_time))->matchday
            ?? $byMatchday->keys()->last();

        $siblings = $season->competitions()->active()->where('category', $competition->category)->get();

        return view('competitions.show', compact('season', 'competition', 'byMatchday', 'currentDay', 'siblings'));
    }
}
