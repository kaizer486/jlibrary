<?php

namespace App\Helpers;

use App\Models\Notification;
use App\Models\User;
use App\Events\NewNotificationEvent;

class LibraryNotificationHelper
{
    /**
     * Send notification to a specific user.
     */
    public static function send($userId, $type, $title, $message, $data = [])
    {
        $notification = Notification::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data,
            'is_read' => false
        ]);

        // Broadcast real-time event
        broadcast(new NewNotificationEvent($notification, $userId))->toOthers();

        return $notification;
    }

    /**
     * Send notification to all librarians in an institution.
     */
    public static function notifyLibrarians($institutionId, $type, $title, $message, $data = [])
    {
        $librarians = User::where('institution_id', $institutionId)
            ->whereHas('roles', function($q) {
                $q->where('name', 'librarian');
            })
            ->get();

        $count = 0;
        foreach ($librarians as $librarian) {
            self::send($librarian->id, $type, $title, $message, $data);
            $count++;
        }

        return $count;
    }

    /**
     * Send notification to all members in an institution.
     */
    public static function notifyMembers($institutionId, $type, $title, $message, $data = [])
    {
        $members = User::where('institution_id', $institutionId)->get();

        $count = 0;
        foreach ($members as $member) {
            self::send($member->id, $type, $title, $message, $data);
            $count++;
        }

        return $count;
    }

    /**
     * Send notification to all users in an institution (including librarians).
     */
    public static function notifyAll($institutionId, $type, $title, $message, $data = [])
    {
        $users = User::where('institution_id', $institutionId)->get();

        $count = 0;
        foreach ($users as $user) {
            self::send($user->id, $type, $title, $message, $data);
            $count++;
        }

        return $count;
    }

    // ==========================================
    // SPECIFIC NOTIFICATION TYPES
    // ==========================================

    /**
     * New book added to library.
     */
    public static function bookAdded($institutionId, $book, $addedBy)
    {
        $data = [
            'book_id' => $book->id,
            'book_title' => $book->title,
            'book_author' => $book->author,
            'added_by' => $addedBy->full_name,
            'institution_id' => $institutionId,
        ];

        // Notify all members
        self::notifyMembers(
            $institutionId,
            Notification::TYPE_LIBRARY_BOOK_ADDED,
            '📚 New Book Added: ' . $book->title,
            "A new book '{$book->title}' by {$book->author} has been added to the library.",
            $data
        );

        // Notify librarian who added it
        self::send(
            $addedBy->id,
            Notification::TYPE_LIBRARY_BOOK_ADDED,
            '✅ Book Added Successfully',
            "You added '{$book->title}' to the library.",
            $data
        );
    }

    /**
     * New join request submitted.
     */
    public static function joinRequest($institutionId, $user, $message)
    {
        $data = [
            'user_id' => $user->id,
            'user_name' => $user->full_name,
            'user_email' => $user->email,
            'message' => $message,
            'institution_id' => $institutionId,
        ];

        // Notify all librarians
        self::notifyLibrarians(
            $institutionId,
            Notification::TYPE_LIBRARY_JOIN_REQUEST,
            '👥 New Join Request',
            "{$user->full_name} has requested to join the library.",
            $data
        );
    }

    /**
     * Join request approved.
     */
    public static function joinApproved($user, $institution)
    {
        $data = [
            'institution_id' => $institution->id,
            'institution_name' => $institution->name,
            'user_id' => $user->id,
        ];

        // Notify the user
        self::send(
            $user->id,
            Notification::TYPE_LIBRARY_JOIN_APPROVED,
            '✅ Welcome to ' . $institution->name . '!',
            "Your request to join {$institution->name} has been approved. You're now a member!",
            $data
        );

        // Notify librarians
        self::notifyLibrarians(
            $institution->id,
            Notification::TYPE_LIBRARY_MEMBER_JOINED,
            '👤 New Member Joined',
            "{$user->full_name} has joined the library.",
            $data
        );
    }

    /**
     * Join request rejected.
     */
    public static function joinRejected($user, $institution, $reason = null)
    {
        $data = [
            'institution_id' => $institution->id,
            'institution_name' => $institution->name,
            'rejection_reason' => $reason,
        ];

        self::send(
            $user->id,
            Notification::TYPE_LIBRARY_JOIN_REJECTED,
            '❌ Join Request Rejected',
            "Your request to join {$institution->name} has been rejected." . ($reason ? " Reason: {$reason}" : ''),
            $data
        );
    }

    /**
     * Book approved.
     */
    public static function bookApproved($institutionId, $book, $approvedBy)
    {
        $data = [
            'book_id' => $book->id,
            'book_title' => $book->title,
            'approved_by' => $approvedBy->full_name,
            'institution_id' => $institutionId,
        ];

        // Notify all members
        self::notifyMembers(
            $institutionId,
            Notification::TYPE_LIBRARY_BOOK_APPROVED,
            '✅ Book Approved: ' . $book->title,
            "The book '{$book->title}' has been approved and is now available.",
            $data
        );
    }

    /**
     * Book rejected.
     */
    public static function bookRejected($institutionId, $book, $reason)
    {
        $data = [
            'book_id' => $book->id,
            'book_title' => $book->title,
            'rejection_reason' => $reason,
            'institution_id' => $institutionId,
        ];

        // Notify the uploader
        $uploader = User::find($book->uploaded_by);
        if ($uploader) {
            self::send(
                $uploader->id,
                Notification::TYPE_LIBRARY_BOOK_REJECTED,
                '❌ Book Rejected: ' . $book->title,
                "Your book '{$book->title}' was rejected. Reason: {$reason}",
                $data
            );
        }
    }

    /**
     * Shelf is getting full.
     */
    public static function shelfFull($institutionId, $shelf)
    {
        $percentage = $shelf->capacity > 0 ? round(($shelf->current_count / $shelf->capacity) * 100) : 0;
        
        $data = [
            'shelf_id' => $shelf->id,
            'shelf_code' => $shelf->code,
            'shelf_name' => $shelf->name,
            'current_count' => $shelf->current_count,
            'capacity' => $shelf->capacity,
            'percentage' => $percentage,
            'institution_id' => $institutionId,
        ];

        self::notifyLibrarians(
            $institutionId,
            Notification::TYPE_LIBRARY_SHELF_FULL,
            '🗄️ Shelf Almost Full: ' . $shelf->code,
            "Shelf '{$shelf->name}' is at {$percentage}% capacity ({$shelf->current_count}/{$shelf->capacity}).",
            $data
        );
    }

    /**
     * Announcement to all members.
     */
    public static function announcement($institutionId, $title, $message, $link = null)
    {
        $data = [
            'link' => $link,
            'institution_id' => $institutionId,
        ];

        self::notifyAll(
            $institutionId,
            Notification::TYPE_LIBRARY_ANNOUNCEMENT,
            '📢 ' . $title,
            $message,
            $data
        );
    }

    /**
     * New feature announcement.
     */
    public static function newFeature($institutionId, $featureName, $description)
    {
        $data = [
            'feature' => $featureName,
            'institution_id' => $institutionId,
        ];

        self::notifyAll(
            $institutionId,
            Notification::TYPE_LIBRARY_NEW_FEATURE,
            ' New Feature: ' . $featureName,
            $description,
            $data
        );
    }
}