<?php

namespace App\Policies;

use App\Models\JoinRequest;
use App\Models\User;

class JoinRequestPolicy
{
    /**
     * Determine if the user can view any join requests.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasInstitution() && $user->isInstitutionAdmin();
    }

    /**
     * Determine if the user can view the join request.
     */
    public function view(User $user, JoinRequest $joinRequest): bool
    {
        if ($user->institution_id === $joinRequest->institution_id) {
            return true;
        }

        return false;
    }

    /**
     * Determine if the user can create join requests.
     */
    public function create(User $user, $institution): bool
    {
        // Cannot request to join if already in an institution
        if ($user->hasInstitution()) {
            return false;
        }

        // Cannot request to join own institution
        if ($user->institution_id === $institution->id) {
            return false;
        }

        return true;
    }

    /**
     * Determine if the user can cancel the join request.
     */
    public function cancel(User $user, JoinRequest $joinRequest): bool
    {
        // Can cancel if they own the request
        if ($user->id === $joinRequest->user_id && $joinRequest->status === 'pending') {
            return true;
        }

        return false;
    }

    /**
     * Determine if the user can approve the join request.
     */
    public function approve(User $user, JoinRequest $joinRequest): bool
    {
        // Can approve if they are an admin of the institution
        if ($user->institution_id === $joinRequest->institution_id && $user->isInstitutionAdmin()) {
            return true;
        }

        return false;
    }

    /**
     * Determine if the user can reject the join request.
     */
    public function reject(User $user, JoinRequest $joinRequest): bool
    {
        // Can reject if they are an admin of the institution
        if ($user->institution_id === $joinRequest->institution_id && $user->isInstitutionAdmin()) {
            return true;
        }

        return false;
    }
}