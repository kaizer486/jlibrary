<?php

namespace App\Http\Controllers\Librarian;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Check if user has librarian role
        if (!auth()->user()->hasRole('librarian')) {
            abort(403, 'Unauthorized access.');
        }
        
        $institution = auth()->user()->institution;
        
        if (!$institution) {
            abort(403, 'You are not associated with any institution.');
        }
        
        $totalBooks = Book::where('institution_id', $institution->id)->count();
        $recentBooks = Book::where('institution_id', $institution->id)->latest()->limit(5)->get();
        
        return view('librarian.dashboard', compact('institution', 'totalBooks', 'recentBooks'));
    }
}