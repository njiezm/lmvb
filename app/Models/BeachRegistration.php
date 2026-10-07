<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BeachRegistration extends Model
{
    public const STATUSES = [
        'pending' => 'En attente',
        'confirmed' => 'Confirmée',
        'cancelled' => 'Annulée',
    ];

    protected $fillable = [
        'beach_event_id', 'team_name', 'player1_name', 'player1_email',
        'player2_name', 'player2_email', 'phone', 'status',
    ];

    public function event()
    {
        return $this->belongsTo(BeachEvent::class, 'beach_event_id');
    }
}
