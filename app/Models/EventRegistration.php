<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventRegistration extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'user_id',
        'status',
        'payment_status',
        'notes',
        'registered_at',
        'cancelled_at',
        'ticket_code',
        'qr_code_path',
        'checked_in_at',
        'checked_in_by',
        'ticket_status',
    ];

    protected $casts = [
        'registered_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'checked_in_at' => 'datetime',
    ];

    // Relations
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function checkedInBy()
    {
        return $this->belongsTo(User::class, 'checked_in_by');
    }

    // Scopes
    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeWaitingList($query)
    {
        return $query->where('status', 'waiting_list');
    }

    // Methods
    public function cancel()
    {
        $this->update([
            'status' => 'cancelled',
            'cancelled_at' => now()
        ]);
        
        $this->event->decrementParticipants();
    }

    public function confirm()
    {
        $this->update([
            'status' => 'confirmed',
            'cancelled_at' => null
        ]);
        
        $this->event->incrementParticipants();
    }

    public static function getStatuses()
    {
        return [
            'confirmed' => 'Confirmée',
            'cancelled' => 'Annulée',
            'waiting_list' => 'Liste d\'attente',
        ];
    }
}
