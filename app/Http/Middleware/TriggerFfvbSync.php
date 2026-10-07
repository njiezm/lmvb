<?php

namespace App\Http\Middleware;

use App\Jobs\SyncFfvbResults;
use App\Services\Ffvb\FfvbImporter;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

/**
 * Mise à jour automatique des résultats lors des visites.
 *
 * Si la dernière synchronisation date de plus de N minutes, la première visite
 * déclenche un import FFVolley exécuté APRÈS l'envoi de la page au visiteur
 * (aucun ralentissement perçu). Complète la tâche cron, sans la remplacer.
 */
class TriggerFfvbSync
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($this->shouldTrigger($request)) {
            SyncFfvbResults::dispatch('visit')->afterResponse();
        }

        return $response;
    }

    private function shouldTrigger(Request $request): bool
    {
        if (! config('lmvb.ffvb.enabled') || ! config('lmvb.ffvb.sync_on_visit')) {
            return false;
        }
        if (! $request->isMethod('GET') || $request->ajax() || $this->isBot($request)) {
            return false;
        }

        $interval = max(5, (int) config('lmvb.ffvb.interval_minutes', 30)) * 60;
        $lastRun = Cache::get(FfvbImporter::LAST_RUN_KEY);
        if ($lastRun && now()->diffInSeconds($lastRun, true) < $interval) {
            return false;
        }

        // Un seul déclenchement par fenêtre, même avec des visites simultanées.
        return Cache::add('ffvb.visit-trigger', true, $interval);
    }

    private function isBot(Request $request): bool
    {
        return (bool) preg_match('/bot|crawl|spider|slurp|facebookexternalhit|preview/i', (string) $request->userAgent());
    }
}
