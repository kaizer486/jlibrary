<?php

namespace App\Events;

use App\Models\InstitutionCreationRequest;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InstitutionCreationRequested
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $creationRequest;

    /**
     * Create a new event instance.
     */
    public function __construct(InstitutionCreationRequest $creationRequest)
    {
        $this->creationRequest = $creationRequest;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('superadmin'),
        ];
    }
}