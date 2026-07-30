<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ActivityMessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $activityId;
    public array $message;

    public function __construct(string $activityId, array $messageData)
    {
        $this->activityId = $activityId;
        $this->message = $messageData;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('activity.' . $this->activityId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'activity.message.sent';
    }
}
