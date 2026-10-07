<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    public const CATEGORIES = [
        'reglement' => 'Règlements',
        'formulaire' => 'Formulaires',
        'calendrier' => 'Calendriers',
        'pv' => "Procès-verbaux & AG",
        'general' => 'Documents généraux',
    ];

    protected $fillable = ['title', 'category', 'file', 'url', 'description', 'active'];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function getLinkAttribute(): ?string
    {
        return $this->file ? asset($this->file) : $this->url;
    }

    public function getCategoryLabelAttribute(): string
    {
        return self::CATEGORIES[$this->category] ?? $this->category;
    }
}
