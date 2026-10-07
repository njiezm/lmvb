<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Competition;
use App\Models\Season;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;

class CompetitionController extends Controller
{
    public function index(Request $request)
    {
        $season = Season::where('slug', $request->query('saison'))->first() ?? Season::current();

        return view('admin.competitions.index', [
            'season' => $season,
            'seasons' => Season::orderByDesc('name')->get(),
            'competitions' => $season
                ? $season->competitions()->withCount(['games', 'standings', 'games as locked_count' => fn ($q) => $q->where('locked', true)])->get()
                : collect(),
        ]);
    }

    public function update(Request $request, Competition $competition)
    {
        $data = $request->validate([
            'name' => 'required|string|max:200',
            'category' => ['required', Rule::in(array_keys(Competition::CATEGORIES))],
            'gender' => 'nullable|in:M,F,X',
            'phase' => ['required', Rule::in(array_keys(Competition::PHASES))],
            'sort_order' => 'required|integer|min:0|max:999',
        ]);
        $data['active'] = $request->boolean('active');

        $competition->update($data);
        Cache::forget('home.data');

        return back()->with('success', 'Compétition mise à jour.');
    }
}
