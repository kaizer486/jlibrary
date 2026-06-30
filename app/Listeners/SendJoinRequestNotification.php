<?php

namespace App\Listeners;

use App\Events\JoinRequestCreated;
use App\Events\JoinRequestApproved;
use App\Events\JoinRequestRejected;
use App\Models\User;
use App\Notifications\JoinRequestNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendJoinRequestNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle($event): void
    {
        if ($event instanceof JoinRequestCreated) {
            $this->handleCreated($event);
        } elseif ($event instanceof JoinRequestApproved) {
            $this->handleApproved($event);
        } elseif ($event instanceof JoinRequestRejected) {
            $this->handleRejected($event);
        }
    }

    /**
     * Handle JoinRequestCreated event.
     */
    protected function handleCreated(JoinRequestCreated $event): void
    {
        $joinRequest = $event->joinRequest;
        
        // ✅ Notify institution admins using Spatie role
        $admins = User::where('institution_id', $joinRequest->institution_id)
            ->role('institution_admin')
            ->get();
        
        foreach ($admins as $admin) {
            $admin->notify(new JoinRequestNotification([
                'type' => 'created',
                'message' => "{$joinRequest->user->full_name} has requested to join your institution.",
                'join_request_id' => $joinRequest->id,
                'institution_id' => $joinRequest->institution_id,
                'user_id' => $joinRequest->user_id,
            ]));
        }
    }

    /**
     * Handle JoinRequestApproved event.
     */
    protected function handleApproved(JoinRequestApproved $event): void
    {
        $joinRequest = $event->joinRequest;
        
        // Notify the user who made the request
        $user = $joinRequest->user;
        $user->notify(new JoinRequestNotification([
            'type' => 'approved',
            'message' => "Your request to join {$joinRequest->institution->name} has been approved!",
            'join_request_id' => $joinRequest->id,
            'institution_id' => $joinRequest->institution_id,
            'institution_name' => $joinRequest->institution->name,
        ]));
    }

    /**
     * Handle JoinRequestRejected event.
     */
    protected function handleRejected(JoinRequestRejected $event): void
    {
        $joinRequest = $event->joinRequest;
        
        // Notify the user who made the request
        $user = $joinRequest->user;
        $user->notify(new JoinRequestNotification([
            'type' => 'rejected',
            'message' => "Your request to join {$joinRequest->institution->name} has been rejected.",
            'join_request_id' => $joinRequest->id,
            'institution_id' => $joinRequest->institution_id,
            'institution_name' => $joinRequest->institution->name,
            'rejection_reason' => $joinRequest->rejection_reason,
        ]));
    }
}