<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ChatSession extends Model
{
    protected $table = 'chat_sessions';

    protected $fillable = [
        'user_id',
        'title',
        'messages',
        'is_pinned',
        'last_message_at'
    ];

    protected $casts = [
        'messages' => 'array',
        'is_pinned' => 'boolean',
        'last_message_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $attributes = [
        'is_pinned' => false,
        'messages' => '[]',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getRecentMessages(int $limit = 20): array
    {
        $messages = $this->messages ?? [];
        return array_slice($messages, -$limit, $limit);
    }

    public function addMessage(string $role, string $content): void
    {
        $messages = $this->messages ?? [];
        $messages[] = [
            'role' => $role,
            'content' => $content,
            'timestamp' => now()->toIso8601String()
        ];

        if (count($messages) > 50) {
            $messages = array_slice($messages, -50);
        }

        $this->messages = $messages;
        $this->last_message_at = now();

        if (empty($this->title) || $this->title === 'New Chat') {
            $firstMessage = $messages[0]['content'] ?? '';
            $this->title = Str::limit($firstMessage, 40);
        }

        $this->save();
    }

    public function scopeRecent($query, int $limit = 30)
    {
        return $query->orderBy('last_message_at', 'desc')
                     ->limit($limit);
    }

    public function hasMessages(): bool
    {
        return !empty($this->messages) && count($this->messages) > 0;
    }

    public function getMessageCount(): int
    {
        return count($this->messages ?? []);
    }

    public function getLastMessage(): ?array
    {
        $messages = $this->messages ?? [];
        return !empty($messages) ? end($messages) : null;
    }

    public function scopeActive($query)
    {
        return $query->whereNotNull('messages')
                     ->whereRaw('JSON_LENGTH(messages) > 0');
    }

    public function scopePinned($query)
    {
        return $query->where('is_pinned', true);
    }
}