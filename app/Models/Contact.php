<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'club_id',
        'name',
        'email',
        'phone',
        'subject',
        'message',
        'read',
        'ip_address',
    ];

    protected $casts = [
        'read' => 'boolean',
    ];

    public function club()
    {
        return $this->belongsTo(Club::class)->withTrashed();
    }

    /** Messages visibles par l'utilisateur : tous pour le super admin, ceux de son club sinon. */
    public function scopeVisibleTo($query, User $user)
    {
        return $user->isSuperAdmin() ? $query : $query->where('club_id', $user->club_id);
    }
}
