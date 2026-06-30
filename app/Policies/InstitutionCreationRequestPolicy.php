<?php

namespace App\Policies;

use App\Models\InstitutionCreationRequest;
use App\Models\User;

class InstitutionCreationRequestPolicy
{
    /**
     * Determine if the user can view any creation requests.
     */
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    /**
     * Determine if the user can view the creation request.
     */
    public function view(User $user, InstitutionCreationRequest $request): bool
    {
        // Superadmin can view all
        if ($user->isSuperAdmin()) {
            return true;
        }

        // User can view their own requests
        if ($user->id === $request->user_id) {
            return true;
        }

        return false;
    }

    /**
     * Determine if the user can create creation requests.
     */
    public function create(User $user): bool
    {
        // Cannot create if already in an institution
        if ($user->hasInstitution()) {
            return false;
        }

        return true;
    }

    /**
     * Determine if the user can cancel the creation request.
     */
    public function cancel(User $user, InstitutionCreationRequest $request): bool
    {
        // Can cancel if they own the request and it's pending
        if ($user->id === $request->user_id && $request->status === 'pending') {
            return true;
        }

        return false;
    }

    /**
     * Determine if the user can approve the creation request.
     */
    public function approve(User $user, InstitutionCreationRequest $request): bool
    {
        return $user->isSuperAdmin() && $request->status === 'pending';
    }

    /**
     * Determine if the user can reject the creation request.
     */
    public function reject(User $user, InstitutionCreationRequest $request): bool
    {
        return $user->isSuperAdmin() && $request->status === 'pending';
    }
}