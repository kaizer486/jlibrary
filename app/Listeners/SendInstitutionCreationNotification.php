<?php

namespace App\Listeners;

use App\Events\InstitutionCreationRequested;
use App\Events\InstitutionCreated;
use App\Models\User;
use App\Notifications\InstitutionCreationNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendInstitutionCreationNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle($event): void
    {
        if ($event instanceof InstitutionCreationRequested) {
            $this->handleRequested($event);
        } elseif ($event instanceof InstitutionCreated) {
            $this->handleCreated($event);
        }
    }

    /**
     * Handle InstitutionCreationRequested event.
     */
    protected function handleRequested(InstitutionCreationRequested $event): void
    {
        $creationRequest = $event->creationRequest;
        
        // ✅ Notify superadmins using Spatie role
        $superadmins = User::role('super_admin')->get();
        
        foreach ($superadmins as $superadmin) {
            $superadmin->notify(new InstitutionCreationNotification([
                'type' => 'requested',
                'message' => "{$creationRequest->user->full_name} has requested to create a new institution.",
                'creation_request_id' => $creationRequest->id,
                'institution_name' => $creationRequest->name,
                'user_id' => $creationRequest->user_id,
            ]));
        }
    }

    /**
     * Handle InstitutionCreated event.
     */
    protected function handleCreated(InstitutionCreated $event): void
    {
        $institution = $event->institution;
        $admin = $event->admin;
        
        // Notify the new institution admin
        $admin->notify(new InstitutionCreationNotification([
            'type' => 'created',
            'message' => "Your institution '{$institution->name}' has been created successfully!",
            'institution_id' => $institution->id,
            'institution_name' => $institution->name,
        ]));
        
        // ✅ Notify superadmins using Spatie role
        $superadmins = User::role('super_admin')->get();
        
        foreach ($superadmins as $superadmin) {
            $superadmin->notify(new InstitutionCreationNotification([
                'type' => 'approved',
                'message' => "Institution '{$institution->name}' has been created and approved.",
                'institution_id' => $institution->id,
                'institution_name' => $institution->name,
                'admin_id' => $admin->id,
            ]));
        }
    }
}