<?php

namespace App\Services\Ffvb;

use App\Models\Club;
use App\Models\Competition;
use App\Models\Game;
use App\Models\Season;
use App\Models\Standing;
use App\Models\SyncLog;
use App\Support\Text;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

/**
 * Importe les compétitions, matchs, résultats et classements de la ligue depuis la FFVolley.
 * Idempotent : chaque match est identifié par (compétition, code match FFVolley).
 * Un match « verrouillé » (corrigé à la main dans l'admin) n'est jamais écrasé.
 */
class FfvbImporter
{
    public const LOCK_KEY = 'ffvb-sync';

    public const LAST_RUN_KEY = 'ffvb.last_run_at';

    private array $clubCache = [];

    private array $stats = ['competitions' => 0, 'created' => 0, 'updated' => 0, 'clubs' => 0];

    private array $errors = [];

    public function __construct(private FfvbClient $client) {}

    /**
     * Lance une synchronisation protégée par un verrou (une seule à la fois).
     * Retourne null si une synchronisation est déjà en cours.
     *
     * @param  array<int, string>  $onlyCodes
     */
    public function sync(?string $seasonName = null, array $onlyCodes = [], string $trigger = 'cron'): ?SyncLog
    {
        $lock = Cache::lock(self::LOCK_KEY, 900);
        if (! $lock->get()) {
            return null;
        }

        try {
            return $this->run($seasonName ?? Season::currentName(), $onlyCodes, $trigger);
        } finally {
            Cache::put(self::LAST_RUN_KEY, now()->toIso8601String());
            $lock->release();
        }
    }

    private function run(string $seasonName, array $onlyCodes, string $trigger): SyncLog
    {
        @set_time_limit(600);
        $this->stats = ['competitions' => 0, 'created' => 0, 'updated' => 0, 'clubs' => 0];
        $this->errors = [];

        $log = SyncLog::create([
            'trigger' => $trigger,
            'season' => $seasonName,
            'status' => 'running',
            'started_at' => now(),
        ]);

        try {
            $season = Season::findOrCreateByName($seasonName);
            if ($seasonName === Season::currentName()) {
                Season::where('id', '!=', $season->id)->update(['is_current' => false]);
                $season->update(['is_current' => true]);
            }

            $list = $this->client->competitions($seasonName);
            if ($onlyCodes) {
                $list = array_values(array_filter($list, fn ($c) => in_array($c['code'], $onlyCodes, true)));
            }

            foreach ($list as $item) {
                try {
                    $this->importCompetition($season, $item);
                    $this->stats['competitions']++;
                } catch (Throwable $e) {
                    $this->errors[] = "{$item['code']} : ".$e->getMessage();
                    Log::warning('Sync FFVolley : échec poule '.$item['code'], ['exception' => $e]);
                }
            }

            $status = $this->errors ? ($this->stats['competitions'] ? 'partial' : 'failed') : 'success';
            $message = $this->errors ? implode("\n", $this->errors) : (count($list) ? null : 'Aucune compétition publiée pour cette saison.');
        } catch (Throwable $e) {
            $status = 'failed';
            $message = $e->getMessage();
            Log::error('Sync FFVolley en échec', ['exception' => $e]);
        }

        $log->update([
            'status' => $status,
            'competitions_count' => $this->stats['competitions'],
            'games_created' => $this->stats['created'],
            'games_updated' => $this->stats['updated'],
            'clubs_created' => $this->stats['clubs'],
            'message' => $message ? Str::limit($message, 2000) : null,
            'finished_at' => now(),
        ]);

        Cache::forget('home.data');

        return $log;
    }

    private function importCompetition(Season $season, array $item): void
    {
        $meta = self::classify($item['name'].' '.($item['group'] ?? ''));

        $competition = Competition::firstOrNew(['season_id' => $season->id, 'code' => $item['code']]);
        $competition->fill([
            'name' => $item['name'],
            'group_name' => $item['group'],
            'category' => $meta['category'],
            'gender' => $meta['gender'],
            'phase' => $meta['phase'],
            'sort_order' => $meta['sort_order'],
            'is_ffvb' => true,
        ]);
        if (! $competition->exists) {
            $competition->slug = $this->uniqueCompetitionSlug($season, $item);
            $competition->active = true;
        }
        $competition->save();

        $rows = $this->client->matches($season->name, $item['code']);
        $teamClubs = [];

        DB::transaction(function () use ($rows, $competition, &$teamClubs) {
            foreach ($rows as $row) {
                $this->importMatch($competition, $row, $teamClubs);
            }
        });

        $standings = $this->client->standings($season->name, $item['code']);
        DB::transaction(function () use ($standings, $competition, $teamClubs) {
            Standing::where('competition_id', $competition->id)->delete();
            foreach ($standings as $row) {
                $clubId = $teamClubs[$row['team_name']] ?? null;
                if ($clubId && $row['link']) {
                    Club::whereKey($clubId)->whereNull('website')->update(['website' => $row['link']]);
                }
                unset($row['link']);
                Standing::create($row + ['competition_id' => $competition->id, 'club_id' => $clubId]);
            }
        });

        $competition->update(['synced_at' => now()]);
    }

    private function importMatch(Competition $competition, array $row, array &$teamClubs): void
    {
        $code = $row['Match'] ?? '';
        $homeName = $row['EQA_nom'] ?? '';
        $awayName = $row['EQB_nom'] ?? '';

        // Exempt (« xxxxx ») : pas un vrai match.
        if ($code === '' || $homeName === 'xxxxx' || $awayName === 'xxxxx' || $homeName === '' || $awayName === '') {
            return;
        }

        $home = $this->club($row['EQA_no'] ?? '', $homeName);
        $away = $this->club($row['EQB_no'] ?? '', $awayName);
        $teamClubs[$homeName] = $home?->id;
        $teamClubs[$awayName] = $away?->id;

        $game = Game::firstOrNew(['competition_id' => $competition->id, 'ffvb_code' => $code]);
        if ($game->exists && $game->locked) {
            return;
        }

        $result = self::parseResult($row['Set'] ?? '', $row['Score'] ?? '', $row['Total'] ?? '');

        $game->fill([
            'matchday' => is_numeric($row['Jo'] ?? null) ? (int) $row['Jo'] : null,
            'date_time' => self::parseDate($row['Date'] ?? '', $row['Heure'] ?? ''),
            'venue' => $row['Salle'] ?: null,
            'home_team_id' => $home?->id,
            'home_team_name' => $homeName,
            'away_team_id' => $away?->id,
            'away_team_name' => $awayName,
            'referee_1' => ($row['Arb1'] ?? '') ?: null,
            'referee_2' => ($row['Arb2'] ?? '') ?: null,
            'competition' => null,
        ] + $result);

        if (! $game->exists) {
            $game->synced_at = now();
            $game->save();
            $this->stats['created']++;
        } elseif ($game->isDirty()) {
            $game->synced_at = now();
            $game->save();
            $this->stats['updated']++;
        }
    }

    /** Club par numéro FFVolley, créé à la volée s'il est inconnu. */
    private function club(string $number, string $teamName): ?Club
    {
        $number = trim($number);
        if ($number === '') {
            return null;
        }
        if (array_key_exists($number, $this->clubCache)) {
            return $this->clubCache[$number];
        }

        $club = Club::withTrashed()->where('ffvb_number', $number)->first();

        if (! $club) {
            $known = config("lmvb.clubs.$number", []);
            $name = $known['name'] ?? Text::title(preg_replace('/\s+\d$/', '', $teamName));
            $slug = Str::slug($name);

            // Club saisi à la main avant l'import (même slug, sans numéro) : on le rattache.
            $club = Club::withTrashed()->whereNull('ffvb_number')->where('slug', $slug)->first();
            if ($club) {
                $club->update(['ffvb_number' => $number]);
            } else {
                $club = Club::create(array_merge([
                    'name' => $name,
                    'slug' => self::uniqueSlug($slug),
                    'color' => '#242868',
                    'active' => true,
                ], array_intersect_key($known, array_flip(['short_name', 'city', 'color', 'founded_year', 'facebook', 'instagram', 'active'])), [
                    'ffvb_number' => $number,
                ]));
                $this->stats['clubs']++;
            }
        }

        return $this->clubCache[$number] = $club;
    }

    // ---------------------------------------------------------------------
    // Helpers de parsing (publics et statiques pour les tests)
    // ---------------------------------------------------------------------

    /**
     * « 3/1 » + « 25-20,23-25,… » + « 98-89 ».
     * Forfait : « 3/F » (l'équipe B est forfait), « F/3 », « F/F ».
     */
    public static function parseResult(string $sets, string $score, string $total): array
    {
        $sets = str_replace(' ', '', $sets);
        if (! preg_match('~^([0-9F])/([0-9F])$~', $sets, $m)) {
            return [
                'status' => 'scheduled', 'home_score' => null, 'away_score' => null,
                'set_scores' => null, 'home_points' => null, 'away_points' => null, 'forfeit' => null,
            ];
        }

        $forfeit = match (true) {
            $m[1] === 'F' && $m[2] === 'F' => 'both',
            $m[1] === 'F' => 'home',
            $m[2] === 'F' => 'away',
            default => null,
        };

        $setScores = [];
        foreach (array_filter(explode(',', $score)) as $set) {
            if (preg_match('/^\s*(\d+)\s*[-:]\s*(\d+)\s*$/', $set, $s)) {
                $setScores[] = [(int) $s[1], (int) $s[2]];
            }
        }

        [$hp, $ap] = preg_match('/^\s*(\d+)\s*-\s*(\d+)\s*$/', $total, $t) ? [(int) $t[1], (int) $t[2]] : [null, null];

        return [
            'status' => 'finished',
            'home_score' => $m[1] === 'F' ? 0 : (int) $m[1],
            'away_score' => $m[2] === 'F' ? 0 : (int) $m[2],
            'set_scores' => $setScores ?: null,
            'home_points' => $hp,
            'away_points' => $ap,
            'forfeit' => $forfeit,
        ];
    }

    public static function parseDate(string $date, string $time): ?Carbon
    {
        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) || $date === '0000-00-00') {
            return null;
        }
        $time = preg_match('/^\d{1,2}:\d{2}$/', $time) ? $time : '00:00';

        return Carbon::createFromFormat('Y-m-d H:i', "$date $time", config('app.timezone'));
    }

    /** Catégorie, genre, phase et ordre d'affichage déduits du libellé de la poule. */
    public static function classify(string $label): array
    {
        $l = Str::upper(Str::ascii($label));

        $category = match (true) {
            str_contains($l, 'BEACH') => 'beach',
            (bool) preg_match('/M18|M20|M21|JUNIOR/', $l) => 'm21',
            (bool) preg_match('/M13|M15|BENJAMIN|MINIME/', $l) => 'm15',
            str_contains($l, 'SENIOR'), str_contains($l, 'CHAMPION'), str_contains($l, 'COUPE') => 'senior',
            default => 'autre',
        };

        $gender = match (true) {
            (bool) preg_match('/FEMININ|FILLES|CHAMPIONNES|DAMES/', $l) => 'F',
            (bool) preg_match('/MASCULIN|GARCONS|CHAMPIONS\b|MESSIEURS/', $l) => 'M',
            str_contains($l, 'MIXTE') => 'X',
            default => null,
        };

        $phase = match (true) {
            str_contains($l, 'PLAY-OFF'), str_contains($l, 'PLAYOFF') => 'playoff',
            str_contains($l, 'PLAY-DOW'), str_contains($l, 'PLAYDOWN') => 'playdown',
            str_contains($l, 'COUPE') => 'cup',
            str_contains($l, 'TOURNOI') => 'tournament',
            default => 'regular',
        };

        $order = ['senior' => 10, 'm21' => 30, 'm15' => 50, 'beach' => 70, 'autre' => 90][$category]
            + ($gender === 'F' ? 0 : 10)
            + ['regular' => 0, 'playoff' => 2, 'playdown' => 3, 'cup' => 5, 'tournament' => 7][$phase];

        return ['category' => $category, 'gender' => $gender, 'phase' => $phase, 'sort_order' => $order];
    }

    private function uniqueCompetitionSlug(Season $season, array $item): string
    {
        $base = Str::slug(Text::title(preg_replace('/\s*-?\s*\d{4}\/\d{4}\s*$/', '', $item['name']))) ?: Str::lower($item['code']);
        $slug = $base;
        if (Competition::where('season_id', $season->id)->where('slug', $slug)->exists()) {
            $slug = $base.'-'.Str::lower($item['code']);
        }

        return $slug;
    }

    private static function uniqueSlug(string $base): string
    {
        $slug = $base ?: 'club';
        $i = 2;
        while (Club::withTrashed()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }
}
