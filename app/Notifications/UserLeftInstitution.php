<?php

namespace App\Notifications;

use App\Models\Institution;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserLeftInstitution extends Notification implements ShouldQueue
{
    use Queueable;

    protected $user;
    protected $institution;

    public function __construct(User $user, Institution $institution)
    {
        $this->user = $user;
        $this->institution = $institution;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toArray($notifiable)
    {
        return [
            'title' => 'User Left Institution',
            'message' => "{$this->user->full_name} has left '{$this->institution->name}'.",
            'user_id' => $this->user->id,
            'institution_id' => $this->institution->id,
            'type' => 'user_left_institution',
        ];
    }
}