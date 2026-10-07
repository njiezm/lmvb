<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Competition extends Model
{
    public const CATEGORIES = [
        'senior' => 'Seniors',
        'm21' => 'M18 / M21',
        'm15' => 'M13 / M15',
        'beach' => 'Beach',
        'autre' => 'Autres',
    ];

    public const PHASES = [
        'regular' => 'Saison régulière',
        'playoff' => 'Play-offs',
        'playdown' => 'Play-down',
        'cup' => 'Coupe',
        'tournament' => 'Tournoi',
    ];

    protected $fillable = [
        'season_id', 'code', 'name', 'slug', 'group_name', 'category', 'gender',
        'phase', 'is_ffvb', 'active', 'sort_order', 'synced_at',
    ];

    protected $casts = [
        'is_ffvb' => 'boolean',
        'active' => 'boolean',
        'synced_at' => 'datetime',
    ];

    public function season()
    {
        return $this->belongsTo(Season::class);
    }

    public function games()
    {
        return $this->hasMany(Game::class);
    }

    public function standings()
    {
        return $this->hasMany(Standing::class)->orderBy('position');
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    public function getCategoryLabelAttribute(): string
    {
        return self::CATEGORIES[$this->category] ?? ucfirst((string) $this->category);
    }

    public function getGenderLabelAttribute(): string
    {
        return match ($this->gender) {
            'M' => 'Masculin',
            'F' => 'Féminin',
            'X' => 'Mixte',
            default => '',
        };
    }

    public function getPhaseLabelAttribute(): string
    {
        return self::PHASES[$this->phase] ?? '';
    }

    /** Nom lisible : « Championnat Senior Masculin – Poule unique ». */
    public function getDisplayNameAttribute(): string
    {
        $name = preg_replace('/\s*-?\s*\d{4}\/\d{4}\s*$/', '', $this->name);
        $name = preg_replace('/\s+20\d{2}\s*$/', '', $name);

        return \App\Support\Text::title($name);
    }

    public function ffvbUrl(): string
    {
        return config('lmvb.ffvb.base_url').'/vbspo_calendrier.php?'.http_build_query([
            'saison' => $this->season->name,
            'codent' => config('lmvb.ffvb.entity'),
            'poule' => $this->code,
        ]);
    }
}
