<?php

namespace App\Models;

use App\Support\Text;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;

    public const STATUSES = [
        'scheduled' => 'Programmé',
        'live' => 'En cours',
        'finished' => 'Terminé',
        'cancelled' => 'Annulé',
    ];

    protected $fillable = [
        'competition_id', 'ffvb_code', 'matchday', 'date_time', 'venue',
        'home_team_id', 'home_team_name', 'away_team_id', 'away_team_name',
        'home_score', 'away_score', 'set_scores', 'home_points', 'away_points', 'forfeit',
        'referee_1', 'referee_2', 'notes', 'status', 'competition', 'locked', 'synced_at',
    ];

    protected $casts = [
        'date_time' => 'datetime',
        'set_scores' => 'array',
        'locked' => 'boolean',
        'synced_at' => 'datetime',
        'home_score' => 'integer',
        'away_score' => 'integer',
    ];

    public function homeTeam()
    {
        return $this->belongsTo(Club::class, 'home_team_id')->withTrashed();
    }

    public function awayTeam()
    {
        return $this->belongsTo(Club::class, 'away_team_id')->withTrashed();
    }

    public function competitionModel()
    {
        return $this->belongsTo(Competition::class, 'competition_id');
    }

    /** Matchs à venir (ou en cours). */
    public function scopeScheduled($query)
    {
        return $query->whereIn('status', ['scheduled', 'live'])
            ->whereNotNull('date_time')
            ->where('date_time', '>=', now()->subHours(3));
    }

    public function scopeFinished($query)
    {
        return $query->where('status', 'finished');
    }

    public function scopeForClub($query, int $clubId)
    {
        return $query->where(fn ($q) => $q->where('home_team_id', $clubId)->orWhere('away_team_id', $clubId));
    }

    public function scopeWithDisplay($query)
    {
        return $query->with(['homeTeam', 'awayTeam', 'competitionModel.season']);
    }

    public function getHomeNameAttribute(): string
    {
        return Text::teamName($this->home_team_name, $this->homeTeam) ?: '—';
    }

    public function getAwayNameAttribute(): string
    {
        return Text::teamName($this->away_team_name, $this->awayTeam) ?: '—';
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function getCompetitionLabelAttribute(): string
    {
        return $this->competitionModel?->display_name ?? ($this->competition ?: 'Match');
    }

    public function getVenueLabelAttribute(): string
    {
        return Text::title($this->venue);
    }

    /** 'home', 'away' ou null. */
    public function getWinnerAttribute(): ?string
    {
        if ($this->status !== 'finished' || $this->home_score === null || $this->away_score === null) {
            return null;
        }

        return $this->home_score > $this->away_score ? 'home' : ($this->away_score > $this->home_score ? 'away' : null);
    }

    public function isForfeit(): bool
    {
        return $this->forfeit !== null;
    }
}
