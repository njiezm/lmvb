<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public const ROLES = [
        'super_admin' => 'Super administrateur',
        'club_admin' => 'Administrateur de club',
    ];

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'club_id',
        'active',
        'avatar',
        'last_login_at',
    ];

    protected $attributes = [
        'role' => 'club_admin',
        'active' => true,
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'active' => 'boolean',
        'password' => 'hashed',
    ];

    public function club()
    {
        return $this->belongsTo(Club::class);
    }

    public function news()
    {
        return $this->hasMany(News::class);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isClubAdmin(): bool
    {
        return $this->role === 'club_admin' && $this->club_id !== null;
    }

    /** Accès au back-office : super admin, ou admin rattaché à un club. */
    public function canAccessAdmin(): bool
    {
        return $this->active && ($this->isSuperAdmin() || $this->isClubAdmin());
    }

    /** Peut gérer les données de ce club. */
    public function managesClub(?int $clubId): bool
    {
        return $this->isSuperAdmin() || ($clubId !== null && $this->club_id === $clubId);
    }

    public function getRoleLabelAttribute(): string
    {
        return self::ROLES[$this->role] ?? 'Aucun accès';
    }
}
