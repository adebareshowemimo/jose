<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatMessage extends Model
{
    public const ROLE_ADMIN = 'admin';
    public const ROLE_EMPLOYER = 'employer';
    public const ROLE_CANDIDATE = 'candidate';

    protected $fillable = [
        'chat_conversation_id',
        'sender_user_id',
        'sender_role',
        'body',
        'action_type',
        'action_payload',
        'read_at',
    ];

    protected function casts(): array
    {
        return [
            'action_payload' => 'array',
            'read_at' => 'datetime',
        ];
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(ChatConversation::class, 'chat_conversation_id');
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_user_id');
    }
}
