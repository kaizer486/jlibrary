<?php

namespace App\Policies;

use App\Models\WithdrawalRequest;
use App\Models\User;

class WithdrawalPolicy
{
    /**
     * Determine if the user can view any withdrawals.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasInstitution() && $user->isInstitutionAdmin();
    }

    /**
     * Determine if the user can view the withdrawal.
     */
    public function view(User $user, WithdrawalRequest $withdrawal): bool
    {
        if ($user->institution_id === $withdrawal->institution_id) {
            return true;
        }

        return false;
    }

    /**
     * Determine if the user can create withdrawals.
     */
    public function create(User $user): bool
    {
        return $user->hasInstitution() && $user->isInstitutionAdmin();
    }

    /**
     * Determine if the user can cancel the withdrawal.
     */
    public function cancel(User $user, WithdrawalRequest $withdrawal): bool
    {
        // Can cancel if pending and belongs to their institution
        if ($user->institution_id === $withdrawal->institution_id && $withdrawal->status === 'pending') {
            return true;
        }

        return false;
    }

    /**
     * Determine if the user can export withdrawals.
     */
    public function export(User $user): bool
    {
        return $user->hasInstitution() && $user->isInstitutionAdmin();
    }
}