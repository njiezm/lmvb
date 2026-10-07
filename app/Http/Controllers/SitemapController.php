<?php

namespace App\Http\Controllers;

use App\Models\BeachEvent;
use App\Models\Club;
use App\Models\Competition;
use App\Models\News;
use App\Models\Team;
use Illuminate\Support\Facades\Cache;

class SitemapController extends Controller
{
    public function __invoke()
    {
        $xml = Cache::remember('sitemap.xml', 3600, function () {
            $urls = collect([
                ['loc' => route('home'), 'priority' => '1.0'],
                ['loc' => route('news.index'), 'priority' => '0.9'],
                ['loc' => route('competitions.index'), 'priority' => '0.9'],
                ['loc' => route('games.index'), 'priority' => '0.8'],
                ['loc' => route('games.results'), 'priority' => '0.8'],
                ['loc' => route('clubs.index'), 'priority' => '0.8'],
                ['loc' => route('beach.index'), 'priority' => '0.7'],
                ['loc' => route('selections.index'), 'priority' => '0.7'],
                ['loc' => route('gallery.index'), 'priority' => '0.6'],
                ['loc' => route('league'), 'priority' => '0.6'],
                ['loc' => route('contact.create'), 'priority' => '0.5'],
            ]);

            News::published()->get(['slug', 'updated_at'])->each(fn ($n) => $urls->push(['loc' => route('news.show', $n->slug), 'lastmod' => $n->updated_at, 'priority' => '0.7']));
            Club::active()->get(['slug', 'updated_at'])->each(fn ($c) => $urls->push(['loc' => route('clubs.show', $c), 'lastmod' => $c->updated_at, 'priority' => '0.6']));
            Competition::active()->with('season')->get()->each(fn ($c) => $urls->push(['loc' => route('competitions.show', [$c->season, $c]), 'lastmod' => $c->synced_at, 'priority' => '0.7']));
            BeachEvent::get(['slug', 'updated_at'])->each(fn ($e) => $urls->push(['loc' => route('beach.show', $e), 'lastmod' => $e->updated_at, 'priority' => '0.5']));
            Team::active()->get(['slug', 'updated_at'])->each(fn ($t) => $urls->push(['loc' => route('selections.show', $t), 'lastmod' => $t->updated_at, 'priority' => '0.5']));

            return view('sitemap', ['urls' => $urls])->render();
        });

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }
}
