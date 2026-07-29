<?php

namespace App\Http\Controllers;

use App\Models\Institution;
use App\Models\Book;
use App\Models\Shelf;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    /**
     * Show public institution library
     */
    public function index($institutionId)
    {
        $institution = Institution::with('books')->findOrFail($institutionId);
        $books = $institution->books()->paginate(20);
        
        return view('public.institution-library', compact('institution', 'books'));
    }
    
    /**
     * Show a single book from institution
     */
    public function show($institutionId, $bookId)
    {
        $institution = Institution::findOrFail($institutionId);
        $book = Book::where('id', $bookId)
            ->whereHas('institutions', function($query) use ($institutionId) {
                $query->where('institution_id', $institutionId);
            })
            ->firstOrFail();
        
        return view('public.institution-book', compact('institution', 'book'));
    }
    
    /**
     * Show shelf books
     */
    public function shelfShow($institutionId, $shelfId)
    {
        $institution = Institution::findOrFail($institutionId);
        $shelf = Shelf::where('id', $shelfId)
            ->where('institution_id', $institutionId)
            ->firstOrFail();
        $books = $shelf->books()->paginate(20);
        
        return view('public.institution-shelf', compact('institution', 'shelf', 'books'));
    }
}