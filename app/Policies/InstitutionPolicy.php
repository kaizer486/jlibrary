<?php

namespace App\Policies;

use App\Models\Institution;
use App\Models\User;

class InstitutionPolicy
{
    /**
     * Determine if the user can view any institutions.
     */
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isAdmin();
    }

    /**
     * Determine if the user can view the institution.
     */
    public function view(User $user, Institution $institution): bool
    {
        // Superadmin can view all institutions
        if ($user->isSuperAdmin()) {
            return true;
        }

        // Admin can view all institutions (but only counts)
        if ($user->isAdmin()) {
            return true;
        }

        // Institution admin can view their own institution
        if ($user->isInstitutionAdmin() && $user->institution_id === $institution->id) {
            return true;
        }

        // Members can view their own institution
        if ($user->institution_id === $institution->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine if the user can create institutions.
     */
    public function create(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    /**
     * Determine if the user can update the institution.
     */
    public function update(User $user, Institution $institution): bool
    {
        return $user->isSuperAdmin();
    }

    /**
     * Determine if the user can delete the institution.
     */
    public function delete(User $user, Institution $institution): bool
    {
        return $user->isSuperAdmin();
    }

    /**
     * Determine if the user can approve the institution.
     */
    public function approve(User $user, Institution $institution): bool
    {
        return $user->isSuperAdmin();
    }

    /**
     * Determine if the user can suspend the institution.
     */
    public function suspend(User $user, Institution $institution): bool
    {
        return $user->isSuperAdmin();
    }

    /**
     * Determine if the user can view members of the institution.
     */
    public function viewMembers(User $user, Institution $institution): bool
    {
        // Superadmin cannot view members (privacy)
        if ($user->isSuperAdmin()) {
            return false;
        }

        // Admin cannot view members (privacy)
        if ($user->isAdmin()) {
            return false;
        }

        // Institution admin can view members of their own institution
        if ($user->isInstitutionAdmin() && $user->institution_id === $institution->id) {
            return true;
        }

        // Members can view members of their own institution
        if ($user->institution_id === $institution->id) {
            return true;
        }

        return false;
    }
}