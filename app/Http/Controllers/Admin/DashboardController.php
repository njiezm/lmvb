<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\Contact;
use App\Models\Game;
use App\Models\News;
use App\Models\Season;
use App\Models\SyncLog;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $clubId = $user->isSuperAdmin() ? null : $user->club_id;

        $newsQuery = News::query()->when($clubId, fn ($q) => $q->where('club_id', $clubId));
        $gamesQuery = fn () => Game::query()->when($clubId, fn ($q) => $q->forClub($clubId));

        $stats = [
            'news_count' => (clone $newsQuery)->count(),
            'published_news_count' => (clone $newsQuery)->published()->count(),
            'clubs_count' => Club::active()->whereNotNull('ffvb_number')->count(),
            'upcoming_games_count' => $gamesQuery()->scheduled()->where('date_time', '<=', now()->addDays(30))->count(),
            'finished_games_count' => $gamesQuery()->finished()->count(),
            'unread_contacts_count' => Contact::visibleTo($user)->where('read', false)->count(),
        ];

        return view('admin.dashboard', [
            'stats' => $stats,
            'club' => $user->club,
            'season' => Season::current(),
            'latestNews' => (clone $newsQuery)->with(['category', 'user'])->latest()->take(5)->get(),
            'latestContacts' => Contact::visibleTo($user)->where('read', false)->latest()->take(5)->get(),
            'upcomingGames' => $gamesQuery()->scheduled()->withDisplay()->orderBy('date_time')->take(6)->get(),
            'recentResults' => $gamesQuery()->finished()->whereNotNull('date_time')->withDisplay()->orderByDesc('date_time')->take(6)->get(),
            'syncLogs' => $user->isSuperAdmin() ? SyncLog::latest('id')->take(8)->get() : collect(),
            'lastSync' => SyncLog::lastSuccess(),
        ]);
    }
}
