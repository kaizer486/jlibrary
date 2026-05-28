<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{

public function index(Request $request)
{
    $query = Book::with('uploader');
    
    // Search
    if ($request->search) {
        $query->where(function($q) use ($request) {
            $q->where('title', 'like', '%' . $request->search . '%')
              ->orWhere('author', 'like', '%' . $request->search . '%');
        });
    }
    
    // Filter by status
    if ($request->status) {
        $query->where('status', $request->status);
    }
    
    // Filter by price type
    if ($request->price_type == 'free') {
        $query->where('is_paid', false);
    } elseif ($request->price_type == 'paid') {
        $query->where('is_paid', true);
    }
    
    // Sort
    switch ($request->sort) {
        case 'oldest':
            $query->oldest();
            break;
        case 'title_asc':
            $query->orderBy('title', 'asc');
            break;
        case 'title_desc':
            $query->orderBy('title', 'desc');
            break;
        default:
            $query->latest();
    }
    
    $books = $query->paginate(15);
    
    // Stats for cards
    $totalBooks = Book::count();
    $approvedBooks = Book::where('status', 'approved')->count();
    $pendingBooks = Book::where('status', 'pending')->count();
    $freeBooks = Book::where('is_paid', false)->count();
    
    return view('admin.books.index', compact('books', 'totalBooks', 'approvedBooks', 'pendingBooks', 'freeBooks'));
}    
    public function create()
    {
        return view('admin.books.create');
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'is_paid' => 'boolean',
            'book_file' => 'required|file|mimes:pdf|max:20480',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'total_pages' => 'nullable|integer|min:0',
        ]);
        
        $bookPath = $request->file('book_file')->store('books', 'public');
        
        $coverPath = null;
        if ($request->hasFile('cover_image')) {
            $coverPath = $request->file('cover_image')->store('book-covers', 'public');
        }
        
        Book::create([
            'title' => $request->title,
            'author' => $request->author,
            'description' => $request->description,
            'price' => $request->price ?? 0,
            'is_paid' => $request->is_paid ?? false,
            'file_path' => $bookPath,
            'cover_image' => $coverPath,
            'total_pages' => $request->total_pages ?? 0,
            'uploaded_by' => auth()->id(),
            'status' => 'approved'
        ]);
        
        return redirect()->route('admin.books.index')->with('success', 'Book added successfully!');
    }
    
    public function show(Book $book)
    {
        $book->load(['uploader', 'ratings.user', 'reviews.user']);
        $book->loadCount(['ratings', 'reviews', 'bookmarks']);
        
        return view('admin.books.show', compact('book'));
    }
    
    public function edit(Book $book)
    {
        return view('admin.books.edit', compact('book'));
    }
    
    public function update(Request $request, Book $book)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'is_paid' => 'boolean',
            'total_pages' => 'nullable|integer|min:0',
        ]);
        
        $book->update([
            'title' => $request->title,
            'author' => $request->author,
            'description' => $request->description,
            'price' => $request->price ?? 0,
            'is_paid' => $request->is_paid ?? false,
            'total_pages' => $request->total_pages ?? 0,
        ]);
        
        if ($request->hasFile('cover_image')) {
            if ($book->cover_image) {
                Storage::disk('public')->delete($book->cover_image);
            }
            $book->cover_image = $request->file('cover_image')->store('book-covers', 'public');
            $book->save();
        }
        
        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Book updated successfully!']);
        }
        
        return redirect()->route('admin.books.index')->with('success', 'Book updated successfully!');
    }
    
    public function destroy(Book $book)
    {
        Storage::disk('public')->delete($book->file_path);
        if ($book->cover_image) {
            Storage::disk('public')->delete($book->cover_image);
        }
        $book->delete();
        
        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Book deleted successfully!']);
        }
        
        return redirect()->route('admin.books.index')->with('success', 'Book deleted successfully!');
    }
    
    public function toggleStatus(Book $book)
    {
        $book->status = $book->status === 'approved' ? 'rejected' : 'approved';
        $book->save();
        
        if (request()->ajax()) {
            return response()->json([
                'success' => true, 
                'status' => $book->status,
                'message' => 'Book ' . $book->status . ' successfully!'
            ]);
        }
        
        return redirect()->back()->with('success', 'Book status updated!');
    }
    
    public function bulkAction(Request $request)
    {
        $action = $request->action;
        $ids = $request->ids;
        
        if (!$ids || !is_array($ids)) {
            return response()->json(['success' => false, 'message' => 'No books selected']);
        }
        
        switch ($action) {
            case 'delete':
                Book::whereIn('id', $ids)->each(function($book) {
                    Storage::disk('public')->delete($book->file_path);
                    if ($book->cover_image) {
                        Storage::disk('public')->delete($book->cover_image);
                    }
                    $book->delete();
                });
                $message = 'Books deleted successfully!';
                break;
            case 'approve':
                Book::whereIn('id', $ids)->update(['status' => 'approved']);
                $message = 'Books approved successfully!';
                break;
            case 'reject':
                Book::whereIn('id', $ids)->update(['status' => 'rejected']);
                $message = 'Books rejected successfully!';
                break;
            default:
                return response()->json(['success' => false, 'message' => 'Invalid action']);
        }
        
        return response()->json(['success' => true, 'message' => $message]);
    }
}