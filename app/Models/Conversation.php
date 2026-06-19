<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject',
        'created_by',
        'is_group',
        'group_type',
        'class_id',
    ];

    protected $casts = [
        'is_group' => 'boolean',
    ];

    protected $appends = [
        'last_message_preview',
        'last_message_time',
        'unread_count',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function lastMessage()
    {
        return $this->hasOne(Message::class)->ofMany('created_at', 'max');
    }

    public function participants()
    {
        return $this->belongsToMany(User::class, 'conversation_user')
            ->withPivot(['role', 'last_read_at', 'is_archived', 'is_pinned', 'notifications_enabled', 'joined_at'])
            ->withTimestamps();
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function scopeForUser($query, $userId)
    {
        return $query->whereHas('participants', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        });
    }

    public function scopeBetweenUsers($query, array $userIds)
    {
        $id = (int) $userIds[0];
        $id2 = (int) ($userIds[1] ?? $userIds[0]);

        return $query->where('is_group', false)->where(function ($q) use ($id, $id2) {
            $q->whereHas('participants', function ($q) use ($id, $id2) {
                $q->select(DB::raw('count(*)'))
                  ->whereIn('user_id', [$id, $id2])
                  ->havingRaw('count(*) = 2');
            });
        });
    }

    public function scopeOrderByPinned($query, $userId)
    {
        return $query->leftJoin('conversation_user', function ($join) use ($userId) {
            $join->on('conversations.id', '=', 'conversation_user.conversation_id')
                ->where('conversation_user.user_id', '=', $userId);
        })->orderByRaw('conversation_user.is_pinned DESC')
          ->orderBy('conversations.updated_at', 'DESC')
          ->select('conversations.*');
    }

    public function getLastMessagePreviewAttribute()
    {
        $last = $this->lastMessage;
        if (!$last) return 'No messages yet';
        if ($last->type === 'file') return '📎 ' . $last->file_name;
        return str($last->body)->limit(60);
    }

    public function getLastMessageTimeAttribute()
    {
        $last = $this->lastMessage;
        if (!$last) return null;
        return $last->created_at;
    }

    public function getUnreadCountAttribute()
    {
        if (!array_key_exists('unread_count', $this->attributes)) {
            $this->loadUnreadCount();
        }
        return $this->attributes['unread_count'] ?? 0;
    }

    public function setUnreadCountAttribute($value)
    {
        $this->attributes['unread_count'] = $value;
    }

    protected function loadUnreadCount()
    {
        $userId = auth()->id();
        if (!$userId) {
            $this->attributes['unread_count'] = 0;
            return;
        }

        $this->attributes['unread_count'] = Message::where('conversation_id', $this->id)
            ->where('sender_id', '!=', $userId)
            ->whereDoesntHave('reads', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->count();
    }

    public function participantUserIds(): array
    {
        return $this->participants()->pluck('users.id')->toArray();
    }

    public function isParticipant($userId): bool
    {
        return $this->participants()->where('user_id', $userId)->exists();
    }

    public function scopeWithUnreadCounts($query, $userId)
    {
        $query->addSelect(['unread_count' => Message::selectRaw('COUNT(*)')
            ->whereColumn('conversation_id', 'conversations.id')
            ->where('sender_id', '!=', $userId)
            ->whereDoesntHave('reads', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            }),
        ]);
    }
}
