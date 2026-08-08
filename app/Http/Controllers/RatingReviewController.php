<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Rating;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RatingReviewController extends Controller
{
    /**
     * Submit or update a rating for a book (standalone).
     */
    public function rate(Request $request, Book $book)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
        ]);

        Rating::updateOrCreate(
            ['user_id' => Auth::id(), 'book_id' => $book->id],
            ['rating' => $request->rating]
        );

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'average' => $book->averageRating(),
                'count' => $book->ratingCount(),
                'user_rating' => $request->rating,
                'message' => 'Thank you for rating this book!',
            ]);
        }

        return redirect()->back()->with('success', 'Thank you for rating this book!');
    }

    /**
     * Submit or update a rating + review together.
     */
    public function review(Request $request, Book $book)
    {
        // Fix: HTML forms send empty strings, but we want true null for optional reviews
        if ($request->has('review') && trim($request->input('review')) === '') {
            $request->merge(['review' => null]);
        }

       $request->validate([
    'rating' => 'required|integer|min:1|max:5',
    'review' => 'nullable|string|max:1000',
]);

        $message = DB::transaction(function () use ($request, $book) {
            // Keep standalone ratings table in sync
            Rating::updateOrCreate(
                ['user_id' => Auth::id(), 'book_id' => $book->id],
                ['rating' => $request->rating]
            );

            $existingReview = Review::where('user_id', Auth::id())
                ->where('book_id', $book->id)
                ->first();

            if ($existingReview) {
                $existingReview->update([
                    'rating' => $request->rating,
                    'review' => $request->review,
                    'is_approved' => true,
                ]);

                return 'Review updated successfully!';
            }

            Review::create([
                'user_id' => Auth::id(),
                'book_id' => $book->id,
                'rating' => $request->rating,
                'review' => $request->review,
                'is_approved' => true,
                'helpful_count' => 0,
            ]);

            return 'Review submitted successfully!';
        });

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'average' => $book->averageRating(),
                'count' => $book->ratingCount(),
            ]);
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Toggle helpful mark on a review.
     */
    public function helpful(Request $request, Review $review)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Please login to mark reviews as helpful.',
            ], 401);
        }

        $existing = $review->helpfulUsers()
            ->where('user_id', $user->id)
            ->exists();

        if ($existing) {
            $review->helpfulUsers()->detach($user->id);
            $review->decrement('helpful_count');
            $liked = false;
            $message = 'Removed helpful mark.';
        } else {
            $review->helpfulUsers()->attach($user->id);
            $review->increment('helpful_count');
            $liked = true;
            $message = 'Marked as helpful!';
        }

        $helpfulCount = $review->helpful_count;

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'helpful_count' => $helpfulCount,
                'liked' => $liked,
                'message' => $message,
            ]);
        }

        return redirect()->back();
    }

    /**
     * Delete user's rating only.
     */
    public function deleteRating(Book $book)
    {
        Rating::where('user_id', Auth::id())
            ->where('book_id', $book->id)
            ->delete();

        return redirect()->back()->with('success', 'Rating removed.');
    }

    /**
     * Delete user's rating + review together.
     */
    public function deleteReview(Request $request, Book $book)
    {
        DB::transaction(function () use ($book) {
            Review::where('user_id', Auth::id())
                ->where('book_id', $book->id)
                ->delete();

            Rating::where('user_id', Auth::id())
                ->where('book_id', $book->id)
                ->delete();
        });

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Rating and review removed.',
            ]);
        }

        return redirect()->back()->with('success', 'Rating and review removed.');
    }
}