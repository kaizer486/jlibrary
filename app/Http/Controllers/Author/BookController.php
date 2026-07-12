<?php

namespace App\Http\Controllers\Author;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BookController extends Controller
{
    public function index()
    {
        $books = Book::where('uploaded_by', auth()->id())
            ->withCount(['ratings', 'reviews'])
            ->latest()
            ->paginate(10);
        
        // Stats
        $stats = [
            'total' => Book::where('uploaded_by', auth()->id())->count(),
            'approved' => Book::where('uploaded_by', auth()->id())->where('status', 'approved')->count(),
            'pending' => Book::where('uploaded_by', auth()->id())->where('status', 'pending')->count(),
            'rejected' => Book::where('uploaded_by', auth()->id())->where('status', 'rejected')->count(),
        ];
        
        return view('author.books.index', compact('books', 'stats'));
    }
    
    public function create()
    {
        return view('author.books.create');
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
            'status' => 'nullable|in:pending,approved,rejected',
        ]);
        
        $bookPath = $request->file('book_file')->store('books', 'public');
        
        $coverPath = null;
        if ($request->hasFile('cover_image')) {
            $coverPath = $request->file('cover_image')->store('book-covers', 'public');
        }
        
        $status = $request->status ?? 'pending';
        
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
            'status' => $status,
        ]);
        
        $message = $status === 'approved' 
            ? '✅ Book uploaded and published successfully!' 
            : '📚 Book uploaded successfully! Awaiting admin approval.';
        
        return redirect()->route('author.books.index')->with('success', $message);
    }
    
    public function edit(Book $book)
    {
        if ($book->uploaded_by !== auth()->id()) {
            abort(403);
        }
        
        return view('author.books.edit', compact('book'));
    }
    
    public function update(Request $request, Book $book)
    {
        if ($book->uploaded_by !== auth()->id()) {
            abort(403);
        }
        
        $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'is_paid' => 'boolean',
            'total_pages' => 'nullable|integer|min:0',
            'status' => 'nullable|in:pending,approved,rejected',
        ]);
        
        $book->update([
            'title' => $request->title,
            'author' => $request->author,
            'description' => $request->description,
            'price' => $request->price ?? 0,
            'is_paid' => $request->is_paid ?? false,
            'total_pages' => $request->total_pages ?? 0,
            'status' => $request->status ?? 'pending',
        ]);
        
        if ($request->hasFile('cover_image')) {
            if ($book->cover_image) {
                Storage::disk('public')->delete($book->cover_image);
            }
            $book->cover_image = $request->file('cover_image')->store('book-covers', 'public');
            $book->save();
        }
        
        return redirect()->route('author.books.index')->with('success', 'Book updated successfully!');
    }
    
    public function destroy(Book $book)
    {
        if ($book->uploaded_by !== auth()->id()) {
            abort(403);
        }
        
        if ($book->file_path) {
            Storage::disk('public')->delete($book->file_path);
        }
        if ($book->cover_image) {
            Storage::disk('public')->delete($book->cover_image);
        }
        
        $book->delete();
        
        return redirect()->route('author.books.index')->with('success', 'Book deleted successfully!');
    }
    
    public function show(Book $book)
    {
        if ($book->uploaded_by !== auth()->id()) {
            abort(403);
        }
        
        $book->load(['ratings.user', 'reviews.user']);
        $book->loadCount(['ratings', 'reviews']);
        
        return view('author.books.show', compact('book'));
    }
}