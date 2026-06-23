<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * A single live notification pushed to logged-in admins. The payload (toast text,
 * deep-link and the fresh badge counts) is prebuilt by {@see \App\Support\AdminNotifier}
 * so this event stays a thin, plain-array carrier.
 *
 * Broadcasts via the queue (ShouldBroadcast) so the request that created the record
 * never waits on — or fails because of — the Reverb HTTP call. Requires a queue worker
 * (`php artisan queue:work`) alongside the Reverb server. The badge counts are snapshotted
 * at dispatch time and carried in the payload, so a slightly-delayed worker still pushes
 * accurate numbers.
 */
class AdminNotification implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    /**
     * @param array<string, mixed> $payload
     */
    public function __construct(public array $payload)
    {
    }

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('admin.notifications');
    }

    public function broadcastAs(): string
    {
        return 'admin.notification';
    }

    /**
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return $this->payload;
    }
}
