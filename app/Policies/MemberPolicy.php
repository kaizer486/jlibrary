<?php

namespace App\Policies;

use App\Models\User;

class MemberPolicy
{
    /**
     * Determine if the user can view any members.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasInstitution() && $user->isInstitutionAdmin();
    }

    /**
     * Determine if the user can view the member.
     */
    public function view(User $user, User $member): bool
    {
        // Can view if they are in the same institution
        if ($user->institution_id === $member->institution_id) {
            return true;
        }

        return false;
    }

    /**
     * Determine if the user can create members.
     */
    public function create(User $user): bool
    {
        return $user->hasInstitution() && $user->isInstitutionAdmin();
    }

    /**
     * Determine if the user can update the member.
     */
    public function update(User $user, User $member): bool
    {
        // Can update if they are in the same institution and are an admin
        if ($user->institution_id === $member->institution_id && $user->isInstitutionAdmin()) {
            return true;
        }

        return false;
    }

    /**
     * Determine if the user can delete the member.
     */
    public function delete(User $user, User $member): bool
    {
        // Cannot delete self
        if ($user->id === $member->id) {
            return false;
        }

        // Can delete if they are in the same institution and are an admin
        if ($user->institution_id === $member->institution_id && $user->isInstitutionAdmin()) {
            return true;
        }

        return false;
    }

    /**
     * Determine if the user can export members.
     */
    public function export(User $user): bool
    {
        return $user->hasInstitution() && $user->isInstitutionAdmin();
    }
}