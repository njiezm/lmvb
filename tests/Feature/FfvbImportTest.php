<?php

namespace Tests\Feature;

use App\Jobs\SyncFfvbResults;
use App\Models\Club;
use App\Models\Competition;
use App\Models\Game;
use App\Models\Standing;
use App\Services\Ffvb\FfvbImporter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class FfvbImportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $fixture = fn ($name) => file_get_contents(base_path("tests/Fixtures/ffvb/$name"));
        Http::fake([
            '*/vbspo_home.php*' => Http::response($fixture('home.html')),
            '*/vbspo_calendrier_export.php*' => fn ($request) => str_contains($request->body(), 'cal_codpoule=CUM')
                ? Http::response($fixture('cum.csv'))
                : Http::response("Entité;Jo;Match;Date;Heure;EQA_no;EQA_nom;EQB_no;EQB_nom;Set;Score;Total;Salle;Arb1;Arb2;\n"),
            '*/vbspo_calendrier.php*' => fn ($request) => str_contains($request->url(), 'poule=CUM')
                ? Http::response($fixture('cum.html'))
                : Http::response('<html></html>'),
        ]);
    }

    public function test_it_imports_matches_standings_and_clubs(): void
    {
        $log = app(FfvbImporter::class)->sync('2025/2026', ['CUM'], 'manual');

        $this->assertSame('success', $log->status);
        $competition = Competition::where('code', 'CUM')->firstOrFail();
        $this->assertSame('senior', $competition->category);

        // 90 lignes dont 18 exempts (« xxxxx ») ignorés
        $this->assertSame(72, Game::count());
        $this->assertSame(72, $log->games_created);
        $this->assertSame(9, Standing::where('competition_id', $competition->id)->count());

        $muc = Club::where('ffvb_number', '9725554')->firstOrFail();
        $this->assertSame('Martinique Université Club', $muc->name); // nom issu de l'annuaire config/lmvb.php
        $this->assertSame(1, Standing::where('club_id', $muc->id)->value('position'));

        $game = Game::where('ffvb_code', 'CUMA002')->firstOrFail();
        $this->assertSame('finished', $game->status);
        $this->assertSame([1, 3], [$game->home_score, $game->away_score]);
        $this->assertSame([[24, 26], [25, 16], [16, 25], [24, 26]], $game->set_scores);
        $this->assertSame('2025-09-27 20:00', $game->date_time->format('Y-m-d H:i'));

        $forfeit = Game::where('ffvb_code', 'CUMA003')->first();
        $this->assertSame('away', $forfeit->forfeit);
    }

    public function test_it_is_idempotent_and_respects_locked_games(): void
    {
        $importer = app(FfvbImporter::class);
        $importer->sync('2025/2026', ['CUM']);

        $game = Game::where('ffvb_code', 'CUMA002')->first();
        $game->update(['home_score' => 3, 'away_score' => 0, 'locked' => true]);

        $log = $importer->sync('2025/2026', ['CUM']);

        $this->assertSame(0, $log->games_created);
        $this->assertSame(0, $log->games_updated);
        $this->assertSame(72, Game::count());
        $this->assertSame(3, $game->fresh()->home_score); // correction manuelle conservée
    }

    public function test_a_visit_triggers_a_background_sync_once_per_interval(): void
    {
        config(['lmvb.ffvb.sync_on_visit' => true]);
        Bus::fake();

        $this->get('/')->assertOk();
        $this->get('/clubs')->assertOk();

        Bus::assertDispatchedTimes(SyncFfvbResults::class, 1);
    }

    public function test_no_visit_sync_when_data_is_fresh(): void
    {
        config(['lmvb.ffvb.sync_on_visit' => true]);
        Cache::put(FfvbImporter::LAST_RUN_KEY, now()->toIso8601String());
        Bus::fake();

        $this->get('/')->assertOk();

        Bus::assertNotDispatched(SyncFfvbResults::class);
    }
}
