<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\Competition;
use App\Models\Game;
use App\Models\Season;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class GameController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $season = Season::where('slug', $request->query('saison'))->first() ?? Season::current();

        $games = Game::withDisplay()
            ->when(! $user->isSuperAdmin(), fn ($q) => $q->forClub($user->club_id))
            ->when($request->query('competition'), fn ($q, $c) => $q->where('competition_id', $c),
                fn ($q) => $q->when($season, fn ($q) => $q->where(fn ($q) => $q
                    ->whereIn('competition_id', $season->competitions()->select('id'))
                    ->orWhereNull('competition_id'))))
            ->when($user->isSuperAdmin() && $request->integer('club'), fn ($q) => $q->forClub($request->integer('club')))
            ->when($request->query('status'), fn ($q, $s) => $q->where('status', $s))
            ->when($request->boolean('locked'), fn ($q) => $q->where('locked', true))
            ->orderByRaw('date_time is null, date_time desc')
            ->paginate(25)->withQueryString();

        return view('admin.games.index', [
            'games' => $games,
            'season' => $season,
            'seasons' => Season::orderByDesc('name')->get(),
            'competitions' => $season ? $season->competitions : collect(),
            'clubs' => Club::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function create()
    {
        return view('admin.games.form', ['game' => new Game(['status' => 'scheduled']), ...$this->formData()]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['locked'] = true; // match saisi à la main : jamais écrasé par l'import

        Game::create($data);

        return redirect()->route('admin.games.index')->with('success', 'Match créé.');
    }

    public function edit(Game $game)
    {
        return view('admin.games.form', ['game' => $game, ...$this->formData()]);
    }

    public function update(Request $request, Game $game)
    {
        $data = $this->validated($request, $game);
        // Un match FFVolley modifié est verrouillé, sauf si l'admin demande explicitement de le resynchroniser.
        $data['locked'] = $game->ffvb_code ? $request->boolean('locked') : true;

        $game->update($data);

        return redirect()->route('admin.games.index', ['competition' => $game->competition_id])->with('success', 'Match mis à jour.');
    }

    public function destroy(Game $game)
    {
        $game->delete();

        return back()->with('success', 'Match supprimé.');
    }

    private function validated(Request $request, ?Game $game = null): array
    {
        $data = $request->validate([
            'competition_id' => ['nullable', Rule::exists('competitions', 'id')],
            'competition' => 'nullable|string|max:100',
            'matchday' => 'nullable|integer|min:0|max:99',
            'date_time' => 'nullable|date',
            'venue' => 'nullable|string|max:150',
            'home_team_id' => 'required|exists:clubs,id',
            'away_team_id' => 'required|exists:clubs,id|different:home_team_id',
            'status' => ['required', Rule::in(array_keys(Game::STATUSES))],
            'home_score' => 'nullable|required_if:status,finished|integer|min:0|max:3',
            'away_score' => 'nullable|required_if:status,finished|integer|min:0|max:3',
            'sets' => 'nullable|array|max:5',
            'sets.*.home' => 'nullable|integer|min:0|max:99',
            'sets.*.away' => 'nullable|integer|min:0|max:99',
            'referee_1' => 'nullable|string|max:100',
            'referee_2' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:2000',
        ], [], ['home_team_id' => 'équipe à domicile', 'away_team_id' => 'équipe à l\'extérieur']);

        $sets = collect($data['sets'] ?? [])
            ->filter(fn ($s) => isset($s['home'], $s['away']) && $s['home'] !== null && $s['away'] !== null)
            ->map(fn ($s) => [(int) $s['home'], (int) $s['away']])
            ->values();
        unset($data['sets']);

        $data['set_scores'] = $sets->isNotEmpty() ? $sets->all() : null;
        $data['home_points'] = $sets->isNotEmpty() ? $sets->sum(0) : null;
        $data['away_points'] = $sets->isNotEmpty() ? $sets->sum(1) : null;

        if ($data['status'] !== 'finished') {
            $data['home_score'] = $data['away_score'] = null;
        }
        // Les noms d'équipes affichés suivent le club choisi pour un match manuel.
        if (! $game?->ffvb_code) {
            $data['home_team_name'] = null;
            $data['away_team_name'] = null;
        }

        return $data;
    }

    private function formData(): array
    {
        return [
            'clubs' => Club::orderBy('name')->get(['id', 'name']),
            'competitions' => Competition::with('season')->orderByDesc('season_id')->ordered()->get(),
        ];
    }
}
