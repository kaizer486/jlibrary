<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Book;
use App\Models\MarketplaceListing;
use App\Models\Payment;
use App\Models\Certificate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;


class DashboardController extends Controller
{

  public function index()
{


    $totalUsers = User::count();
    $totalBooks = Book::count();
    $totalListings = MarketplaceListing::count();
    $pendingListings = MarketplaceListing::where('status', 'pending')->count();
    $totalCertificates = Certificate::count();
    
    // Handle payments table safely (if it exists)
    $totalRevenue = 0;
    if (Schema::hasTable('payments')) {
        $totalRevenue = Payment::where('status', 'completed')->sum('amount');
    }
    
    // Recent data
    $recentUsers = User::latest()->limit(5)->get();
    $recentBooks = Book::latest()->limit(5)->get();
    $recentListings = MarketplaceListing::with('seller')->latest()->limit(5)->get();
    
    return view('admin.dashboard', compact(
        'totalUsers', 'totalBooks', 'totalListings', 'pendingListings',
        'totalCertificates', 'totalRevenue', 'recentUsers', 'recentBooks',
        'recentListings'
    ));
}
    
    private function getChartData()
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $data['labels'][] = $date->format('M d');
            $data['users'][] = User::whereDate('created_at', $date)->count();
            $data['books'][] = Book::whereDate('created_at', $date)->count();
        }
        return $data;
    }
}