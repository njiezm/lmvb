<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/** Équipe de sélection régionale (sélections de Martinique). */
class Team extends Model
{
    use HasFactory;

    public const CATEGORIES = [
        'senior_m' => 'Senior Masculin',
        'senior_f' => 'Senior Féminin',
        'u18_m' => 'U18 / U23 Masculin',
        'u18_f' => 'U18 / U23 Féminin',
        'u16_m' => 'U16 Masculin',
        'u16_f' => 'U16 Féminin',
    ];

    protected $fillable = [
        'name',
        'slug',
        'category',
        'description',
        'coach',
        'photo',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function players()
    {
        return $this->hasMany(Player::class)->orderBy('number')->orderBy('last_name');
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeSenior($query)
    {
        return $query->whereIn('category', ['senior_m', 'senior_f']);
    }

    public function scopeYouth($query)
    {
        return $query->whereIn('category', ['u18_m', 'u18_f', 'u16_m', 'u16_f']);
    }

    public function getCategoryNameAttribute(): string
    {
        return self::CATEGORIES[$this->category] ?? $this->category;
    }

    public function getGenderAttribute(): string
    {
        return str_ends_with($this->category, '_f') ? 'F' : 'M';
    }
}
