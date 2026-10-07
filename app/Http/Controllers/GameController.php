<?php

namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\Competition;
use App\Models\Game;
use App\Models\Season;
use Illuminate\Http\Request;

class GameController extends Controller
{
    /** Matchs à venir. */
    public function index(Request $request)
    {
        $games = $this->filtered($request, Game::scheduled())
            ->orderBy('date_time')
            ->paginate(20)->withQueryString();

        return view('games.index', $this->filters($request) + ['games' => $games]);
    }

    public function results(Request $request)
    {
        $games = $this->filtered($request, Game::finished()->whereNotNull('date_time'))
            ->orderByDesc('date_time')
            ->paginate(20)->withQueryString();

        return view('games.results', $this->filters($request) + ['games' => $games]);
    }

    /** Calendrier : matchs à venir regroupés par mois. */
    public function schedule(Request $request)
    {
        $games = $this->filtered($request, Game::scheduled())
            ->orderBy('date_time')
            ->limit(300)
            ->get()
            ->groupBy(fn ($game) => $game->date_time->translatedFormat('F Y'));

        return view('games.schedule', $this->filters($request) + ['months' => $games]);
    }

    public function show(Game $game)
    {
        $game->load(['homeTeam', 'awayTeam', 'competitionModel.season']);

        $headToHead = Game::finished()->withDisplay()
            ->where('id', '!=', $game->id)
            ->when($game->home_team_id && $game->away_team_id, fn ($q) => $q->where(fn ($q) => $q
                ->where(fn ($q) => $q->where('home_team_id', $game->home_team_id)->where('away_team_id', $game->away_team_id))
                ->orWhere(fn ($q) => $q->where('home_team_id', $game->away_team_id)->where('away_team_id', $game->home_team_id))
            ), fn ($q) => $q->whereRaw('1 = 0'))
            ->orderByDesc('date_time')->take(5)->get();

        return view('games.show', compact('game', 'headToHead'));
    }

    private function filtered(Request $request, $query)
    {
        return $query->withDisplay()
            ->when($request->integer('club'), fn ($q, $club) => $q->forClub($club))
            ->when($request->query('categorie'), fn ($q, $cat) => $q->whereHas('competitionModel', fn ($c) => $c->where('category', $cat)))
            ->when(in_array($request->query('genre'), ['M', 'F'], true), fn ($q) => $q->whereHas('competitionModel', fn ($c) => $c->where('gender', $request->query('genre'))));
    }

    private function filters(Request $request): array
    {
        return [
            'clubs' => Club::active()->whereNotNull('ffvb_number')->orderBy('name')->get(['id', 'name']),
            'categories' => Competition::CATEGORIES,
            'season' => Season::current(),
            'selected' => [
                'club' => $request->integer('club') ?: null,
                'categorie' => $request->query('categorie'),
                'genre' => $request->query('genre'),
            ],
        ];
    }
}
