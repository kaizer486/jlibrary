<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        
        // ==========================================
        // INSTITUTION EVENTS & LISTENERS
        // ==========================================
        \App\Events\JoinRequestCreated::class => [
            \App\Listeners\SendJoinRequestNotification::class,
        ],
        \App\Events\JoinRequestApproved::class => [
            \App\Listeners\SendJoinRequestNotification::class,
        ],
        \App\Events\JoinRequestRejected::class => [
            \App\Listeners\SendJoinRequestNotification::class,
        ],
        \App\Events\InstitutionCreationRequested::class => [
            \App\Listeners\SendInstitutionCreationNotification::class,
        ],
        \App\Events\InstitutionCreated::class => [
            \App\Listeners\SendInstitutionCreationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}