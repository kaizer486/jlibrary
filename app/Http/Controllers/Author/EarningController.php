<?php

namespace App\Http\Controllers\Author;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Payment;

class EarningController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $bookIds = Book::where('uploaded_by', $user->id)->pluck('id');
        
        // Book royalties (10% commission)
        $totalBookSales = Payment::where('status', 'completed')
            ->where('payable_type', 'App\\Models\\Book')
            ->whereIn('payable_id', $bookIds)
            ->sum('amount');
            
        $totalRoyalties = $totalBookSales * 0.10;
        
        // Monthly breakdown
        $monthlyRoyalties = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthlySales = Payment::where('status', 'completed')
                ->where('payable_type', 'App\\Models\\Book')
                ->whereIn('payable_id', $bookIds)
                ->whereMonth('created_at', $i)
                ->whereYear('created_at', date('Y'))
                ->sum('amount');
            $monthlyRoyalties[] = $monthlySales * 0.10;
        }
        
        $recentRoyalties = Payment::where('status', 'completed')
            ->where('payable_type', 'App\\Models\\Book')
            ->whereIn('payable_id', $bookIds)
            ->with('payable', 'user')
            ->latest()
            ->limit(20)
            ->get();
        
        return view('author.earnings', compact('totalRoyalties', 'monthlyRoyalties', 'recentRoyalties'));
    }
}