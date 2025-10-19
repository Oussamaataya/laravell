<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is regular user
     */
    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    /**
     * Check if user is active
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Scope for active users
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for admin users
     */
    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    /**
     * Update last login timestamp
     */
    public function updateLastLogin()
    {
        $this->update(['last_login_at' => now()]);
    }

    /**
     * Get the publications for the user.
     */
    public function publications()
    {
        return $this->hasMany(Publication::class);
    }

    /**
     * Get the commentaires for the user.
     */
    public function commentaires()
    {
        return $this->hasMany(Commentaire::class);
    }

    /**
     * Get the likes for the user.
     */
    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    /**
     * Get the event registrations for the user.
     */
    public function eventRegistrations()
    {
        return $this->hasMany(EventRegistration::class);
    }

    /**
     * Get the chat rooms created by the user.
     */
    public function createdChatRooms()
    {
        return $this->hasMany(ChatRoom::class, 'created_by');
    }

    /**
     * Get the chat rooms where the user participates.
     */
    public function chatRooms()
    {
        return $this->belongsToMany(ChatRoom::class, 'chat_participants', 'user_id', 'chat_room_id')
                    ->withPivot(['role', 'joined_at', 'last_seen', 'is_muted', 'is_banned'])
                    ->withTimestamps();
    }

    /**
     * Get the chat messages sent by the user.
     */
    public function chatMessages()
    {
        return $this->hasMany(ChatMessage::class);
    }

    /**
     * Get the chat participations of the user.
     */
    public function chatParticipations()
    {
        return $this->hasMany(ChatParticipant::class);
    }
}
