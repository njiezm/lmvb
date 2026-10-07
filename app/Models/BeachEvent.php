<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BeachEvent extends Model
{
    use HasFactory;

    public const TYPES = [
        'tournament' => 'Tournoi',
        'training' => 'Entraînement / stage',
        'exhibition' => 'Exhibition',
    ];

    public const STATUSES = [
        'upcoming' => 'À venir',
        'ongoing' => 'En cours',
        'finished' => 'Terminé',
        'cancelled' => 'Annulé',
    ];

    protected $fillable = [
        'title',
        'slug',
        'description',
        'image',
        'start_date',
        'end_date',
        'location',
        'type',
        'category',
        'status',
        'max_teams',
        'registered_teams',
        'prize_pool',
        'registration_open',
        'contact_email',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'registration_open' => 'boolean',
        'max_teams' => 'integer',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function registrations()
    {
        return $this->hasMany(BeachRegistration::class);
    }

    public function scopeUpcoming($query)
    {
        return $query->whereIn('status', ['upcoming', 'ongoing'])->where('end_date', '>=', now());
    }

    public function scopeFinished($query)
    {
        return $query->where(fn ($q) => $q->where('status', 'finished')->orWhere('end_date', '<', now()))
            ->where('status', '!=', 'cancelled');
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function getSpotsLeftAttribute(): ?int
    {
        return $this->max_teams ? max(0, $this->max_teams - $this->registered_teams) : null;
    }

    public function canRegister(): bool
    {
        return $this->registration_open
            && $this->status === 'upcoming'
            && $this->start_date?->isFuture()
            && ($this->max_teams === null || $this->registered_teams < $this->max_teams);
    }
}
