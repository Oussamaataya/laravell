<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'chat_room_id',
        'user_id',
        'message',
        'type',
        'file_path',
        'file_name',
        'file_size',
        'reply_to',
        'is_edited',
        'edited_at',
        'is_deleted',
        'deleted_at',
        'metadata'
    ];

    protected $casts = [
        'is_edited' => 'boolean',
        'is_deleted' => 'boolean',
        'edited_at' => 'datetime',
        'deleted_at' => 'datetime',
        'metadata' => 'array'
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

    public function replyTo()
    {
        return $this->belongsTo(ChatMessage::class, 'reply_to');
    }

    public function replies()
    {
        return $this->hasMany(ChatMessage::class, 'reply_to');
    }

    public function reads()
    {
        return $this->hasMany(ChatMessageRead::class);
    }

    // Scopes
    public function scopeNotDeleted($query)
    {
        return $query->where('is_deleted', false);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeRecent($query, $hours = 24)
    {
        return $query->where('created_at', '>=', now()->subHours($hours));
    }

    // Méthodes utilitaires
    public function isText(): bool
    {
        return $this->type === 'text';
    }

    public function isFile(): bool
    {
        return in_array($this->type, ['image', 'file']);
    }

    public function isSystem(): bool
    {
        return $this->type === 'system';
    }

    public function canEdit(User $user): bool
    {
        if ($this->is_deleted) return false;
        if ($this->user_id !== $user->id) return false;
        if ($this->created_at->diffInMinutes(now()) > 30) return false;
        
        return true;
    }

    public function canDelete(User $user): bool
    {
        if ($this->is_deleted) return false;
        
        // L'auteur peut supprimer son message
        if ($this->user_id === $user->id) return true;
        
        // Les modérateurs peuvent supprimer les messages
        $participant = $this->chatRoom->participants()
                           ->where('user_id', $user->id)
                           ->first();
        
        return $participant && $participant->isModerator();
    }

    public function markAsRead(User $user)
    {
        return $this->reads()->firstOrCreate([
            'user_id' => $user->id
        ], [
            'read_at' => now()
        ]);
    }

    public function isReadBy(User $user): bool
    {
        return $this->reads()->where('user_id', $user->id)->exists();
    }

    public function getReadCount(): int
    {
        return $this->reads()->count();
    }

    public function edit(string $newMessage)
    {
        $this->update([
            'message' => $newMessage,
            'is_edited' => true,
            'edited_at' => now()
        ]);
    }

    public function softDelete()
    {
        $this->update([
            'is_deleted' => true,
            'deleted_at' => now()
        ]);
    }

    public function getFileUrl(): ?string
    {
        if (!$this->file_path) return null;
        
        return asset('storage/' . $this->file_path);
    }

    public function getFileSizeFormatted(): ?string
    {
        if (!$this->file_size) return null;
        
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }
}
