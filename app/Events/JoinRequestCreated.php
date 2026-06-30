<?php

namespace App\Events;

use App\Models\JoinRequest;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class JoinRequestCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $joinRequest;

    /**
     * Create a new event instance.
     */
    public function __construct(JoinRequest $joinRequest)
    {
        $this->joinRequest = $joinRequest;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('institution.' . $this->joinRequest->institution_id),
        ];
    }
}