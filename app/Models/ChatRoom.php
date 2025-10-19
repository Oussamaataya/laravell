<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ChatRoom extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'type',
        'created_by',
        'room_code',
        'max_participants',
        'is_active',
        'settings',
        'last_activity'
    ];

    protected $casts = [
        'settings' => 'array',
        'is_active' => 'boolean',
        'last_activity' => 'datetime'
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($room) {
            if (empty($room->room_code)) {
                $room->room_code = strtoupper(Str::random(8));
            }
        });
    }

    // Relations
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function participants()
    {
        return $this->hasMany(ChatParticipant::class);
    }

    public function activeParticipants()
    {
        return $this->participants()->where('is_banned', false);
    }

    public function messages()
    {
        return $this->hasMany(ChatMessage::class)->where('is_deleted', false);
    }

    public function latestMessage()
    {
        return $this->hasOne(ChatMessage::class)->latest();
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'chat_participants', 'chat_room_id', 'user_id')
                    ->withPivot(['role', 'joined_at', 'last_seen', 'is_muted', 'is_banned'])
                    ->withTimestamps();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePublic($query)
    {
        return $query->where('type', 'public');
    }

    public function scopePrivate($query)
    {
        return $query->where('type', 'private');
    }

    // Méthodes utilitaires
    public function canJoin(User $user): bool
    {
        if (!$this->is_active) return false;
        
        $participantCount = $this->activeParticipants()->count();
        if ($participantCount >= $this->max_participants) return false;
        
        $participant = $this->participants()->where('user_id', $user->id)->first();
        if ($participant && $participant->is_banned) return false;
        
        return true;
    }

    public function addParticipant(User $user, string $role = 'member'): ChatParticipant
    {
        return $this->participants()->create([
            'user_id' => $user->id,
            'role' => $role,
            'joined_at' => now()
        ]);
    }

    public function removeParticipant(User $user): bool
    {
        return $this->participants()->where('user_id', $user->id)->delete();
    }

    public function isParticipant(User $user): bool
    {
        return $this->participants()
                    ->where('user_id', $user->id)
                    ->where('is_banned', false)
                    ->exists();
    }

    public function getParticipantRole(User $user): ?string
    {
        $participant = $this->participants()->where('user_id', $user->id)->first();
        return $participant ? $participant->role : null;
    }

    public function updateLastActivity()
    {
        $this->update(['last_activity' => now()]);
    }

    public function getUnreadCount(User $user): int
    {
        $lastSeen = $this->participants()
                         ->where('user_id', $user->id)
                         ->value('last_seen');
        
        if (!$lastSeen) return 0;
        
        return $this->messages()
                    ->where('created_at', '>', $lastSeen)
                    ->where('user_id', '!=', $user->id)
                    ->count();
    }
}
