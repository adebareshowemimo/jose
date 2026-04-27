<?php

namespace App\Events;

use App\Models\ChatMessage;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatMessageSent implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public function __construct(public ChatMessage $message)
    {
        $this->message->loadMissing(['sender', 'conversation']);
    }

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('chat.conversation.'.$this->message->chat_conversation_id);
    }

    public function broadcastAs(): string
    {
        return 'message.sent';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->message->id,
            'conversation_id' => $this->message->chat_conversation_id,
            'sender_user_id' => $this->message->sender_user_id,
            'sender_role' => $this->message->sender_role,
            'sender_name' => $this->message->sender?->name,
            'body' => $this->message->body,
            'action_type' => $this->message->action_type,
            'action_payload' => $this->message->action_payload,
            'created_at' => $this->message->created_at?->toIso8601String(),
        ];
    }
}
