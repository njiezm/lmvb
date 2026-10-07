<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Ffvb\FfvbImporter;
use Illuminate\Http\Request;

/** Synchronisation FFVolley lancée à la demande depuis l'admin. */
class SyncController extends Controller
{
    public function store(Request $request, FfvbImporter $importer)
    {
        $data = $request->validate([
            'season' => ['nullable', 'regex:~^\d{4}/\d{4}$~'],
            'poule' => ['nullable', 'regex:/^[A-Z0-9]{2,10}$/'],
        ]);

        $log = $importer->sync($data['season'] ?? null, array_filter([$data['poule'] ?? null]), 'manual');

        if (! $log) {
            return back()->with('error', 'Une synchronisation est déjà en cours, réessayez dans quelques minutes.');
        }

        $summary = "Synchronisation {$log->season} : {$log->competitions_count} poule(s), {$log->games_created} match(s) ajouté(s), {$log->games_updated} mis à jour.";

        return back()->with($log->status === 'failed' ? 'error' : 'success', $log->status === 'failed' ? 'Échec : '.$log->message : $summary);
    }
}
