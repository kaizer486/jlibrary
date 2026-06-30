<?php

namespace App\Helpers;

use App\Models\Notification;
use App\Events\NewNotificationEvent;

class NotificationHelper
{
    public static function send($userId, $type, $title, $message, $data = [])
    {
        // Save to database
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

    public static function joinRequestSent($adminId, $userName, $joinRequestId, $institutionId)
{
    self::send(
        $adminId,
        'join_request',
        '👋 New Join Request',
        "{$userName} wants to join your institution!",
        [
            'join_request_id' => $joinRequestId,
            'institution_id' => $institutionId,
            'type' => 'join_request',
            'link' => route('institution.join-requests.index')
        ]
    );
}

public static function joinRequestApproved($userId, $institutionName, $institutionId)
{
    self::send(
        $userId,
        'join_approved',
        '✅ Join Request Approved',
        "Your request to join {$institutionName} has been approved!",
        [
            'institution_id' => $institutionId,
            'type' => 'join_approved',
            'link' => route('institution.dashboard')
        ]
    );
}

public static function joinRequestRejected($userId, $institutionName, $reason = null)
{
    $message = "Your request to join {$institutionName} was rejected.";
    if ($reason) {
        $message .= " Reason: {$reason}";
    }
    
    self::send(
        $userId,
        'join_rejected',
        '❌ Join Request Rejected',
        $message,
        ['type' => 'join_rejected']
    );
}

    public static function bookPurchased($buyerId, $sellerId, $bookTitle, $bookId)
    {
        // Notify seller
        self::send(
            $sellerId,
            'purchase',
            '🎉 Book Sold!',
            "Someone purchased your book '{$bookTitle}'",
            ['book_id' => $bookId, 'type' => 'purchase']
        );

        // Notify buyer (optional)
        self::send(
            $buyerId,
            'purchase',
            '📚 Purchase Confirmed',
            "You have successfully purchased '{$bookTitle}'",
            ['book_id' => $bookId, 'type' => 'purchase_confirmation']
        );
    }

    public static function certificateEarned($userId, $quizTitle, $certificateId)
    {
        self::send(
            $userId,
            'certificate',
            '🎓 Certificate Earned!',
            "Congratulations! You earned a certificate for '{$quizTitle}'",
            ['certificate_id' => $certificateId, 'type' => 'certificate']
        );
    }

    public static function quizPassed($userId, $quizTitle, $score, $quizId)
    {
        self::send(
            $userId,
            'quiz',
            '🧠 Quiz Mastered!',
            "You passed '{$quizTitle}' with {$score}%!",
            ['quiz_id' => $quizId, 'score' => $score, 'type' => 'quiz']
        );
    }

    public static function bookApproved($userId, $bookTitle, $bookId)
    {
        self::send(
            $userId,
            'book_approval',
            '✅ Book Approved',
            "Your book '{$bookTitle}' has been approved and is now live!",
            ['book_id' => $bookId, 'type' => 'approval']
        );
    }

    public static function bookRejected($userId, $bookTitle, $reason)
    {
        self::send(
            $userId,
            'book_approval',
            '❌ Book Rejected',
            "Your book '{$bookTitle}' was rejected. Reason: {$reason}",
            ['type' => 'rejection']
        );
    }

    public static function newReview($userId, $reviewerName, $bookTitle, $bookId)
    {
        self::send(
            $userId,
            'review',
            '⭐ New Review',
            "{$reviewerName} reviewed your book '{$bookTitle}'",
            ['book_id' => $bookId, 'type' => 'review']
        );
    }
}