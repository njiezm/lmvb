<?php

namespace App\Jobs;

use App\Services\Ffvb\FfvbImporter;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

/** Synchronisation FFVolley exécutable en tâche de fond (après la réponse HTTP ou via la file). */
class SyncFfvbResults implements ShouldQueue
{
    use Queueable;

    public int $timeout = 600;

    public function __construct(
        public string $trigger = 'visit',
        public ?string $season = null,
    ) {}

    public function handle(FfvbImporter $importer): void
    {
        ignore_user_abort(true);
        $importer->sync($this->season, [], $this->trigger);
    }
}
