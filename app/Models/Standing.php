<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Standing extends Model
{
    protected $fillable = [
        'competition_id', 'club_id', 'team_name', 'position', 'points', 'played', 'won', 'lost',
        'forfeits', 'w30', 'w31', 'w32', 'l23', 'l13', 'l03', 'sets_for', 'sets_against',
        'points_for', 'points_against',
    ];

    public function competition()
    {
        return $this->belongsTo(Competition::class);
    }

    public function club()
    {
        return $this->belongsTo(Club::class);
    }

    public function getSetRatioAttribute(): ?float
    {
        return $this->sets_against ? round($this->sets_for / $this->sets_against, 3) : null;
    }

    public function getDisplayNameAttribute(): string
    {
        return \App\Support\Text::teamName($this->team_name, $this->club);
    }
}
