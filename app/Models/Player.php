<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    use HasFactory;

    public const POSITIONS = [
        'Passeur' => 'Passeur / Passeuse',
        'Pointu' => 'Pointu(e)',
        'Réceptionneur-attaquant' => 'Réceptionneur·se-attaquant·e',
        'Central' => 'Central(e)',
        'Libéro' => 'Libéro',
    ];

    protected $fillable = [
        'first_name',
        'last_name',
        'position',
        'number',
        'height',
        'birth_date',
        'photo',
        'team_id',
        'club_name',
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getAgeAttribute(): ?int
    {
        return $this->birth_date?->age;
    }
}
