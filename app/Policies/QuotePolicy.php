<?php

namespace App\Policies;

use App\Models\Quote;
use App\Models\User;

class QuotePolicy
{
    /**
     * Determine if the user can view any quotes.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasInstitution();
    }

    /**
     * Determine if the user can view the quote.
     */
    public function view(User $user, Quote $quote): bool
    {
        if ($user->institution_id === $quote->institution_id) {
            return true;
        }

        return false;
    }

    /**
     * Determine if the user can create quotes.
     */
    public function create(User $user): bool
    {
        return $user->hasInstitution() && $user->isInstitutionAdmin();
    }

    /**
     * Determine if the user can update the quote.
     */
    public function update(User $user, Quote $quote): bool
    {
        if ($user->institution_id === $quote->institution_id && $user->isInstitutionAdmin()) {
            return true;
        }

        return false;
    }

    /**
     * Determine if the user can delete the quote.
     */
    public function delete(User $user, Quote $quote): bool
    {
        if ($user->institution_id === $quote->institution_id && $user->isInstitutionAdmin()) {
            return true;
        }

        return false;
    }

    /**
     * Determine if the user can export quotes.
     */
    public function export(User $user): bool
    {
        return $user->hasInstitution() && $user->isInstitutionAdmin();
    }
}