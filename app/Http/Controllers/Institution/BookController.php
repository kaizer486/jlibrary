<?php

namespace App\Http\Controllers\Institution;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index()
    {
        $institution = auth()->user()->institution;
        
        $books = Book::where('institution_id', $institution->id)->latest()->paginate(15);
        
        return view('institution.books.index', compact('books', 'institution'));
    }
}