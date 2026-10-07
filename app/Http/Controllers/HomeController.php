<?php

namespace App\Http\Controllers;

use App\Models\BeachEvent;
use App\Models\Club;
use App\Models\Competition;
use App\Models\Gallery;
use App\Models\Game;
use App\Models\News;
use App\Models\Partner;
use App\Models\Season;
use App\Models\SyncLog;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function index()
    {
        // Données mises en cache 5 min ; le cache est vidé à chaque import FFVolley.
        $data = Cache::remember('home.data', 300, function () {
            $season = Season::current();

            $leagueCompetitions = $season
                ? Competition::where('season_id', $season->id)->active()
                    ->where('category', 'senior')->where('phase', 'regular')
                    ->has('standings')->ordered()
                    ->with(['standings.club', 'season'])
                    ->get()
                : collect();

            return [
                'season' => $season,
                'featuredNews' => News::published()->featured()->with('category')->newest()->take(1)->get(),
                'latestNews' => News::published()->with('category', 'club')->newest()->take(4)->get(),
                'upcomingGames' => Game::scheduled()->withDisplay()->orderBy('date_time')->take(6)->get(),
                'latestResults' => Game::finished()->whereNotNull('date_time')->withDisplay()->orderByDesc('date_time')->take(6)->get(),
                'standings' => $leagueCompetitions,
                'clubs' => Club::active()->whereNotNull('ffvb_number')->orderBy('name')->get(),
                'videos' => Gallery::active()->where('type', 'video')->latest()->take(3)->get(),
                'photos' => Gallery::active()->where('type', 'image')->latest()->take(6)->get(),
                'nextBeach' => BeachEvent::upcoming()->orderBy('start_date')->first(),
                'partners' => Partner::active()->get(),
                'stats' => [
                    'clubs' => Club::active()->whereNotNull('ffvb_number')->count(),
                    'competitions' => $season ? $season->competitions()->count() : 0,
                    'games' => $season ? Game::whereIn('competition_id', $season->competitions()->select('id'))->finished()->count() : 0,
                    'previous_games' => Game::finished()->count(),
                ],
                'lastSync' => SyncLog::lastSuccess()?->finished_at,
            ];
        });

        return view('home', $data);
    }
}
