<?php

use App\Models\ChatConversation;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('chat.conversation.{conversationId}', function ($user, int $conversationId) {
    $conversation = ChatConversation::with(['candidate.user'])->find($conversationId);

    if (! $conversation) {
        return false;
    }

    if ($user->role?->name === 'admin') {
        return $conversation->type === ChatConversation::TYPE_ADMIN_CANDIDATE;
    }

    if ($conversation->type === ChatConversation::TYPE_EMPLOYER_CANDIDATE) {
        if ($user->role?->name === 'employer') {
            return (int) $user->company?->id === (int) $conversation->company_id;
        }

        if ($user->role?->name === 'candidate') {
            return (int) $user->candidate?->id === (int) $conversation->candidate_id;
        }
    }

    if ($conversation->type === ChatConversation::TYPE_ADMIN_CANDIDATE && $user->role?->name === 'candidate') {
        return (int) $user->candidate?->id === (int) $conversation->candidate_id;
    }

    return false;
});
