<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Institution;
use App\Models\User;
use App\Models\Book;
use App\Models\Quote;
use App\Models\WithdrawalRequest;
use App\Models\JoinRequest;
use App\Models\InstitutionCreationRequest;
use App\Policies\InstitutionPolicy;
use App\Policies\MemberPolicy;
use App\Policies\BookPolicy;
use App\Policies\QuotePolicy;
use App\Policies\WithdrawalPolicy;
use App\Policies\JoinRequestPolicy;
use App\Policies\InstitutionCreationRequestPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Institution::class => InstitutionPolicy::class,
        User::class => MemberPolicy::class,
        Book::class => BookPolicy::class,
        Quote::class => QuotePolicy::class,
        WithdrawalRequest::class => WithdrawalPolicy::class,
        JoinRequest::class => JoinRequestPolicy::class,
        InstitutionCreationRequest::class => InstitutionCreationRequestPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Superadmin Gate - can do everything
        Gate::before(function ($user, $ability) {
            if ($user->isSuperAdmin()) {
                return true;
            }
        });

        // Institution Admin Gate
        Gate::define('manage-institution', function ($user, $institution) {
            return $user->isInstitutionAdmin() && $user->institution_id === $institution->id;
        });

        // View Member Gate (privacy)
        Gate::define('view-member', function ($user, $member) {
            // Superadmin cannot view members (privacy)
            if ($user->isSuperAdmin()) {
                return false;
            }
            
            // Admin cannot view members (privacy)
            if ($user->isAdmin()) {
                return false;
            }
            
            // Same institution
            return $user->institution_id === $member->institution_id;
        });
    }
}