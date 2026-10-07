<?php

namespace App\Console\Commands;

use App\Models\Season;
use App\Services\Ffvb\FfvbImporter;
use Illuminate\Console\Command;

class SyncFfvb extends Command
{
    protected $signature = 'lmvb:sync-ffvb
        {--season= : Saison au format 2025/2026 (par défaut : saison en cours)}
        {--poule=* : Limiter à certaines poules (codes FFVolley, ex. PUM)}
        {--trigger=cron : Origine (cron, manual)}';

    protected $description = 'Importe matchs, résultats et classements de la ligue depuis la FFVolley';

    public function handle(FfvbImporter $importer): int
    {
        if (! config('lmvb.ffvb.enabled')) {
            $this->warn('Synchronisation désactivée (FFVB_SYNC_ENABLED=false).');

            return self::SUCCESS;
        }

        $season = $this->option('season') ?: Season::currentName();
        if (! preg_match('~^\d{4}/\d{4}$~', $season)) {
            $this->error('Format de saison attendu : 2025/2026');

            return self::INVALID;
        }

        $this->info("Synchronisation FFVolley — saison {$season}…");
        $log = $importer->sync($season, $this->option('poule'), $this->option('trigger'));

        if (! $log) {
            $this->warn('Une synchronisation est déjà en cours.');

            return self::SUCCESS;
        }

        $this->table(['Statut', 'Poules', 'Matchs créés', 'Matchs mis à jour', 'Clubs créés'], [[
            $log->status, $log->competitions_count, $log->games_created, $log->games_updated, $log->clubs_created,
        ]]);
        if ($log->message) {
            $this->line($log->message);
        }

        return $log->status === 'failed' ? self::FAILURE : self::SUCCESS;
    }
}
