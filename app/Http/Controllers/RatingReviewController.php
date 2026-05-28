<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Rating;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RatingReviewController extends Controller
{
    
    /**
     * Submit or update a rating for a book
     */
    public function rate(Request $request, Book $book)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5'
        ]);

        $rating = Rating::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'book_id' => $book->id
            ],
            ['rating' => $request->rating]
        );

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'average' => $book->averageRating(),
                'count' => $book->ratingCount(),
                'user_rating' => $request->rating,
                'message' => 'Thank you for rating this book!'
            ]);
        }

        return redirect()->back()->with('success', 'Thank you for rating this book!');
    }

    /**
     * Submit a review for a book
     */
    public function review(Request $request, Book $book)
    {
        $request->validate([
            'review' => 'required|string|min:10|max:1000'
        ]);

        $existingReview = Review::where('user_id', Auth::id())
            ->where('book_id', $book->id)
            ->first();

        if ($existingReview) {
            $existingReview->update([
                'review' => $request->review,
                'is_approved' => false
            ]);
            $message = 'Review updated! Waiting for approval.';
        } else {
            Review::create([
                'user_id' => Auth::id(),
                'book_id' => $book->id,
                'review' => $request->review,
                'is_approved' => false
            ]);
            $message = 'Review submitted! It will appear after approval.';
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Mark a review as helpful
     */
    public function helpful(Review $review)
    {
        $review->increment('helpful_count');
        
        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'helpful_count' => $review->helpful_count
            ]);
        }
        
        return redirect()->back();
    }

    /**
     * Delete user's rating
     */
    public function deleteRating(Book $book)
    {
        Rating::where('user_id', Auth::id())
            ->where('book_id', $book->id)
            ->delete();

        return redirect()->back()->with('success', 'Rating removed.');
    }

    /**
     * Delete user's review
     */
    public function deleteReview(Book $book)
    {
        Review::where('user_id', Auth::id())
            ->where('book_id', $book->id)
            ->delete();

        return redirect()->back()->with('success', 'Review deleted.');
    }
}