<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    use HasFactory;

    public const CATEGORIES = [
        'match' => 'Match',
        'beach' => 'Beach',
        'team' => 'Équipe',
        'event' => 'Événement',
        'training' => 'Entraînement',
        'selection' => 'Sélection',
    ];

    protected $fillable = [
        'club_id',
        'title',
        'type',
        'image',
        'video_url',
        'category',
        'description',
        'credit',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function club()
    {
        return $this->belongsTo(Club::class)->withTrashed();
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function getCategoryNameAttribute(): string
    {
        return self::CATEGORIES[$this->category] ?? ucfirst((string) $this->category);
    }

    public function getImageUrlAttribute(): ?string
    {
        if ($this->image) {
            return asset($this->image);
        }

        return $this->youtube_id ? "https://i.ytimg.com/vi/{$this->youtube_id}/hqdefault.jpg" : null;
    }

    public function getYoutubeIdAttribute(): ?string
    {
        if (! $this->video_url) {
            return null;
        }
        preg_match('~(?:youtu\.be/|v=|embed/|shorts/)([A-Za-z0-9_-]{11})~', $this->video_url, $m);

        return $m[1] ?? null;
    }

    public function isVideo(): bool
    {
        return $this->type === 'video';
    }
}
