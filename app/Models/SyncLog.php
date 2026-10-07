<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SyncLog extends Model
{
    protected $fillable = [
        'source', 'trigger', 'season', 'status', 'competitions_count', 'games_created',
        'games_updated', 'clubs_created', 'message', 'started_at', 'finished_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public static function lastSuccess(): ?self
    {
        return static::whereIn('status', ['success', 'partial'])->latest('finished_at')->first();
    }
}
