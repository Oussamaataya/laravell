<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatParticipant extends Model
{
    use HasFactory;

    protected $fillable = [
        'chat_room_id',
        'user_id',
        'role',
        'joined_at',
        'last_seen',
        'is_muted',
        'is_banned',
        'permissions'
    ];

    protected $casts = [
        'joined_at' => 'datetime',
        'last_seen' => 'datetime',
        'is_muted' => 'boolean',
        'is_banned' => 'boolean',
        'permissions' => 'array'
    ];

    // Relations
    public function chatRoom()
    {
        return $this->belongsTo(ChatRoom::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_banned', false);
    }

    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    public function scopeModerators($query)
    {
        return $query->whereIn('role', ['admin', 'moderator']);
    }

    // Méthodes utilitaires
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isModerator(): bool
    {
        return in_array($this->role, ['admin', 'moderator']);
    }

    public function canSendMessages(): bool
    {
        return !$this->is_muted && !$this->is_banned;
    }

    public function canModerate(): bool
    {
        return $this->isModerator() && !$this->is_banned;
    }

    public function updateLastSeen()
    {
        $this->update(['last_seen' => now()]);
    }

    public function mute()
    {
        $this->update(['is_muted' => true]);
    }

    public function unmute()
    {
        $this->update(['is_muted' => false]);
    }

    public function ban()
    {
        $this->update(['is_banned' => true]);
    }

    public function unban()
    {
        $this->update(['is_banned' => false]);
    }
}
