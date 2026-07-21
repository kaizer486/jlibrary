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
        // ADMINS & SUPER ADMINS - Can see ALL books from ANY institution
        if ($user->hasRole(['admin', 'super_admin'])) {
            return true;
        }
        
        // Institution admins - can see their institution's books
        if ($user->isInstitutionAdmin() && $user->hasInstitution()) {
            return true;
        }
        
        return false;
    }

    /**
     * Determine if the user can view the book.
     */
    public function view(User $user, Book $book): bool
    {
        // ADMINS & SUPER ADMINS - Can see ALL books from ANY institution
        if ($user->hasRole(['admin', 'super_admin'])) {
            return true;
        }
        
        // INSTITUTION ADMINS - Can see their institution's books AND global books
        if ($user->isInstitutionAdmin() && $user->hasInstitution()) {
            return $user->institution_id === $book->institution_id || $book->institution_id === null;
        }
        
        // REGULAR USERS - Can see their institution's books AND global books
        if ($user->hasInstitution()) {
            return $user->institution_id === $book->institution_id || $book->institution_id === null;
        }
        
        // Users with no institution can only see global books
        return $book->institution_id === null;
    }

    /**
     * Determine if the user can view the book cover image.
     * Cover images are public - accessible to everyone.
     */
    public function viewCover(User $user, Book $book): bool
    {
        return true;
    }

    /**
     * Determine if the user can create books.
     */
    public function create(User $user): bool
    {
        // Admins and Super Admins can create books for any institution
        if ($user->hasRole(['admin', 'super_admin'])) {
            return true;
        }
        
        // Institution admins can create books for their institution
        return $user->hasInstitution() && $user->isInstitutionAdmin();
    }

    /**
     * Determine if the user can update the book.
     */
    public function update(User $user, Book $book): bool
    {
        // Admins and Super Admins can update ANY book
        if ($user->hasRole(['admin', 'super_admin'])) {
            return true;
        }
        
        // Institution admins can update their institution's books
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
        // Admins and Super Admins can delete ANY book
        if ($user->hasRole(['admin', 'super_admin'])) {
            return true;
        }
        
        // Institution admins can delete their institution's books
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
        // Admins and Super Admins can export ALL books
        if ($user->hasRole(['admin', 'super_admin'])) {
            return true;
        }
        
        return $user->hasInstitution() && $user->isInstitutionAdmin();
    }

    /**
     * Determine if the user can approve books.
     */
    public function approve(User $user, Book $book): bool
    {
        // Admins and Super Admins can approve ANY book
        if ($user->hasRole(['admin', 'super_admin'])) {
            return true;
        }
        
        // Institution admins can approve their institution's books
        if ($user->institution_id === $book->institution_id && $user->isInstitutionAdmin()) {
            return true;
        }

        return false;
    }
}