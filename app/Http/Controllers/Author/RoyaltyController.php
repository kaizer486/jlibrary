<?php

namespace App\Http\Controllers\Author;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Payment;
use App\Models\WithdrawalRequest;
use Illuminate\Http\Request;

class RoyaltyController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Get author's book IDs
        $bookIds = Book::where('uploaded_by', $user->id)->pluck('id');
        
        // Get all royalty transactions
        $royalties = Payment::where('status', 'completed')
            ->where('payable_type', 'App\\Models\\Book')
            ->whereIn('payable_id', $bookIds)
            ->with('user')
            ->latest()
            ->paginate(20);
        
        // Calculate totals
        $totalRoyalties = Payment::where('status', 'completed')
            ->where('payable_type', 'App\\Models\\Book')
            ->whereIn('payable_id', $bookIds)
            ->sum('amount') * 0.10;
        
        $totalWithdrawn = WithdrawalRequest::where('user_id', $user->id)
            ->where('status', 'completed')
            ->sum('amount');
        
        $available = $totalRoyalties - $totalWithdrawn;
        
        return view('author.royalties.index', compact('royalties', 'totalRoyalties', 'totalWithdrawn', 'available'));
    }
}