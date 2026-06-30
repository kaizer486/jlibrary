<?php

namespace App\Policies;

use App\Models\Book;
use App\Models\User;

class BookPolicy
{
    /**
     * Determine if the user can view any books.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasInstitution();
    }

    /**
     * Determine if the user can view the book.
     */
    public function view(User $user, Book $book): bool
    {
        // Can view if they are in the same institution
        if ($user->institution_id === $book->institution_id) {
            return true;
        }

        return false;
    }

    /**
     * Determine if the user can view the book cover image.
     * Cover images are public - accessible to everyone.
     */
    public function viewCover(User $user, Book $book): bool
    {
        // Allow public access to cover images
        // No login or institution check required
        return true;
    }

    /**
     * Determine if the user can create books.
     */
    public function create(User $user): bool
    {
        return $user->hasInstitution() && $user->isInstitutionAdmin();
    }

    /**
     * Determine if the user can update the book.
     */
    public function update(User $user, Book $book): bool
    {
        // Can update if they are in the same institution and are an admin
        if ($user->institution_id === $book->institution_id && $user->isInstitutionAdmin()) {
            return true;
        }

        return false;
    }

    /**
     * Determine if the user can delete the book.
     */
    public function delete(User $user, Book $book): bool
    {
        // Can delete if they are in the same institution and are an admin
        if ($user->institution_id === $book->institution_id && $user->isInstitutionAdmin()) {
            return true;
        }

        return false;
    }

    /**
     * Determine if the user can export books.
     */
    public function export(User $user): bool
    {
        return $user->hasInstitution() && $user->isInstitutionAdmin();
    }

    /**
     * Determine if the user can approve books.
     */
    public function approve(User $user, Book $book): bool
    {
        // Can approve if they are in the same institution and are an admin
        if ($user->institution_id === $book->institution_id && $user->isInstitutionAdmin()) {
            return true;
        }

        return false;
    }
}