<?php

namespace App\Models;

use App\Support\Text;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Club extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'ffvb_number', 'name', 'short_name', 'slug', 'description', 'city', 'venue', 'address',
        'phone', 'email', 'website', 'facebook', 'instagram', 'logo', 'color', 'members_count',
        'founded_year', 'active',
    ];

    protected $casts = [
        'active' => 'boolean',
        'members_count' => 'integer',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function homeGames()
    {
        return $this->hasMany(Game::class, 'home_team_id');
    }

    public function awayGames()
    {
        return $this->hasMany(Game::class, 'away_team_id');
    }

    public function standings()
    {
        return $this->hasMany(Standing::class);
    }

    public function news()
    {
        return $this->hasMany(News::class);
    }

    public function admins()
    {
        return $this->hasMany(User::class);
    }

    public function photos()
    {
        return $this->hasMany(Gallery::class);
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function getInitialsAttribute(): string
    {
        return $this->short_name && mb_strlen($this->short_name) <= 4
            ? mb_strtoupper($this->short_name)
            : Text::initials($this->name);
    }

    public function getLogoUrlAttribute(): ?string
    {
        return $this->logo ? asset($this->logo) : null;
    }

    public function getDisplayColorAttribute(): string
    {
        return $this->color ?: '#242868';
    }
}
