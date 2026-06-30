<?php

namespace App\Events;

use App\Models\Institution;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InstitutionCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $institution;
    public $admin;

    /**
     * Create a new event instance.
     */
    public function __construct(Institution $institution, User $admin)
    {
        $this->institution = $institution;
        $this->admin = $admin;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->admin->id),
            new PrivateChannel('superadmin'),
        ];
    }
}