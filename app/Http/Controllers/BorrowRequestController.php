<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Institution;
use App\Models\BorrowRequest;
use App\Models\Borrowing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BorrowRequestController extends Controller
{
    public function create($book_id, $institution_id)
    {
        $book = Book::findOrFail($book_id);
        $institution = Institution::findOrFail($institution_id);
        
        // Check if user already has a pending request
        $hasPendingRequest = BorrowRequest::where('book_id', $book->id)
            ->where('user_id', Auth::id())
            ->where('status', 'pending')
            ->exists();
        
        if ($hasPendingRequest) {
            return redirect()->route('institution.public.show', [$institution->id, $book->id])
                ->with('error', 'You already have a pending request for this book.');
        }
        
        // Check if user already has an active borrowing
        $hasActiveBorrowing = Borrowing::where('book_id', $book->id)
            ->where('user_id', Auth::id())
            ->where('status', 'borrowed')
            ->exists();
        
        if ($hasActiveBorrowing) {
            return redirect()->route('institution.public.show', [$institution->id, $book->id])
                ->with('error', 'You already have this book borrowed.');
        }
        
        return view('institution.public.borrow-request', compact('book', 'institution'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'book_id' => 'required|exists:books,id',
            'institution_id' => 'required|exists:institutions,id',
            'duration_days' => 'required|integer|in:7,14,21,30',
            'reason' => 'required|string|min:10|max:1000',
            'terms' => 'required|accepted',
        ]);

        // Check for existing requests
        $existingRequest = BorrowRequest::where('book_id', $request->book_id)
            ->where('user_id', Auth::id())
            ->whereIn('status', ['pending', 'approved'])
            ->exists();

        if ($existingRequest) {
            return redirect()->route('institution.public.show', [$request->institution_id, $request->book_id])
                ->with('error', 'You already have a pending or approved request for this book.');
        }

        // Check for active borrowing
        $existingBorrowing = Borrowing::where('book_id', $request->book_id)
            ->where('user_id', Auth::id())
            ->where('status', 'borrowed')
            ->exists();

        if ($existingBorrowing) {
            return redirect()->route('institution.public.show', [$request->institution_id, $request->book_id])
                ->with('error', 'You already have this book borrowed.');
        }

        // Create borrow request
        BorrowRequest::create([
            'book_id' => $request->book_id,
            'user_id' => Auth::id(),
            'institution_id' => $request->institution_id,
            'duration_days' => $request->duration_days,
            'reason' => $request->reason,
            'status' => 'pending',
            'requested_at' => now(),
        ]);

        // Redirect back to book page with success message
        return redirect()->route('institution.public.show', [$request->institution_id, $request->book_id])
            ->with('success', '✅ Your borrow request has been submitted successfully! Please wait for librarian approval.');
    }
}