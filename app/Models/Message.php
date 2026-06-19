<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Message extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'conversation_id',
        'sender_id',
        'body',
        'read_at',
        'parent_id',
        'forwarded_from',
        'type',
        'file_path',
        'file_name',
        'file_size',
        'file_mime',
        'metadata',
        'edited_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
        'edited_at' => 'datetime',
        'metadata' => 'array',
        'file_size' => 'integer',
        'deleted_at' => 'datetime',
    ];

    protected $appends = [
        'is_edited',
        'file_url',
        'file_icon',
    ];

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function parent()
    {
        return $this->belongsTo(Message::class, 'parent_id');
    }

    public function forwardedFrom()
    {
        return $this->belongsTo(Message::class, 'forwarded_from');
    }

    public function reactions()
    {
        return $this->hasMany(MessageReaction::class);
    }

    public function reads()
    {
        return $this->hasMany(MessageRead::class);
    }

    public function replies()
    {
        return $this->hasMany(Message::class, 'parent_id');
    }

    public function scopeUnreadByUser($query, $userId)
    {
        return $query->where('sender_id', '!=', $userId)
            ->whereDoesntHave('reads', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            });
    }

    public function isEdited(): bool
    {
        return !is_null($this->edited_at);
    }

    public function getIsEditedAttribute(): bool
    {
        return $this->isEdited();
    }

    public function getFileUrlAttribute(): ?string
    {
        if ($this->file_path) {
            return Storage::url($this->file_path);
        }
        return null;
    }

    public function getFileIconAttribute(): string
    {
        if (!$this->file_mime) return 'fa-regular fa-file';

        return match (true) {
            str_contains($this->file_mime, 'pdf') => 'fa-regular fa-file-pdf text-red-500',
            str_contains($this->file_mime, 'word') => 'fa-regular fa-file-word text-blue-500',
            str_contains($this->file_mime, 'excel') || str_contains($this->file_mime, 'spreadsheet') => 'fa-regular fa-file-excel text-green-500',
            str_contains($this->file_mime, 'presentation') || str_contains($this->file_mime, 'powerpoint') => 'fa-regular fa-file-powerpoint text-orange-500',
            str_contains($this->file_mime, 'image') => 'fa-regular fa-file-image text-purple-500',
            str_contains($this->file_mime, 'video') => 'fa-regular fa-file-video text-pink-500',
            str_contains($this->file_mime, 'zip') || str_contains($this->file_mime, 'rar') || str_contains($this->file_mime, '7z') => 'fa-regular fa-file-archive text-yellow-500',
            default => 'fa-regular fa-file text-gray-500',
        };
    }

    public function isOwnedBy($userId): bool
    {
        return $this->sender_id === $userId;
    }

    public function isEditable(): bool
    {
        $timeout = config('messaging.message_edit_timeout', 10);
        return !$this->trashed() && $this->created_at->diffInMinutes() < $timeout;
    }

    public function scopeForUser($query, $userId)
    {
        return $query->whereHas('conversation.participants', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        });
    }

    public function markReadBy($userId): void
    {
        if ($this->sender_id === $userId) return;

        MessageRead::firstOrCreate([
            'message_id' => $this->id,
            'user_id' => $userId,
        ], [
            'read_at' => now(),
        ]);
    }

    public function isReadBy($userId): bool
    {
        return $this->reads()->where('user_id', $userId)->exists();
    }

    protected static function booted(): void
    {
        static::deleting(function (Message $message) {
            if ($message->file_path && Storage::exists($message->file_path)) {
                Storage::delete($message->file_path);
            }
        });
    }
}
