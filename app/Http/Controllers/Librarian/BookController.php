<?php

namespace App\Http\Controllers\Librarian;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Shelf;
use App\Models\Institution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use App\Helpers\LibraryNotificationHelper;

class BookController extends Controller
{
    /**
     * Display a listing of books.
     */
    public function index(Request $request)
    {
        $institution = auth()->user()->institution;

        if (!$institution) {
            abort(403, 'You do not belong to any institution.');
        }

        $query = Book::where('institution_id', $institution->id);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhere('author', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by shelf
        if ($request->filled('shelf')) {
            $query->where('shelf_number', $request->shelf);
        }

        // Filter by category
        if ($request->filled('category') && Schema::hasColumn('books', 'category')) {
            $query->where('category', $request->category);
        }

        $books = $query->latest()->paginate(15)->appends($request->query());

        // Get shelves for filter
        $shelves = Shelf::where('institution_id', $institution->id)
            ->where('status', 'active')
            ->get();

        // Get categories
        $categories = collect();
        if (Schema::hasColumn('books', 'category')) {
            $categories = Book::where('institution_id', $institution->id)
                ->whereNotNull('category')
                ->select('category')
                ->distinct()
                ->pluck('category');
        }

        // Stats
        $stats = [
            'total' => Book::where('institution_id', $institution->id)->count(),
            'approved' => Book::where('institution_id', $institution->id)->where('status', 'approved')->count(),
            'pending' => Book::where('institution_id', $institution->id)->where('status', 'pending')->count(),
            'rejected' => Book::where('institution_id', $institution->id)->where('status', 'rejected')->count(),
        ];

        return view('librarian.books.index', compact('books', 'institution', 'shelves', 'categories', 'stats'));
    }

    /**
     * Show the form for creating a new book.
     */
    public function create()
    {
        $institution = auth()->user()->institution;

        if (!$institution) {
            abort(403, 'You do not belong to any institution.');
        }

        $shelves = Shelf::where('institution_id', $institution->id)
            ->where('status', 'active')
            ->get();

        return view('librarian.books.create', compact('institution', 'shelves'));
    }

    /**
     * Store a newly created book.
     */
    public function store(Request $request)
    {
        $institution = auth()->user()->institution;

        if (!$institution) {
            abort(403, 'You do not belong to any institution.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'is_paid' => 'boolean',
            'price' => 'nullable|numeric|min:0',
            'status' => 'required|in:pending,approved,rejected',
            'shelf_number' => 'nullable|string|max:50|exists:shelves,code',
            'shelf_name' => 'nullable|string|max:100',
            'column_location' => 'nullable|string|max:100',
            'position' => 'nullable|string|max:100',
            'floor' => 'nullable|string|max:50',
            'section' => 'nullable|string|max:100',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'file' => 'nullable|file|mimes:pdf|max:10240',
            'total_pages' => 'nullable|integer|min:1',
        ]);

        // Prepare data
        $data = [
            'institution_id' => $institution->id,
            'uploaded_by' => auth()->id(),
            'title' => $validated['title'],
            'author' => $validated['author'] ?? null,
            'category' => $validated['category'] ?? null,
            'description' => $validated['description'] ?? null,
            'is_paid' => $validated['is_paid'] ?? false,
            'price' => $validated['price'] ?? 0,
            'status' => $validated['status'] ?? 'pending',
            'shelf_number' => $validated['shelf_number'] ?? null,
            'shelf_name' => $validated['shelf_name'] ?? null,
            'column_location' => $validated['column_location'] ?? null,
            'position' => $validated['position'] ?? null,
            'floor' => $validated['floor'] ?? null,
            'section' => $validated['section'] ?? null,
            'total_pages' => $validated['total_pages'] ?? 0,
        ];

        // Handle cover image
        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('books/covers', 'public');
        }

        // Handle PDF file
        if ($request->hasFile('file')) {
            $data['file_path'] = $request->file('file')->store('books/pdfs', 'public');
        }

        $book = Book::create($data);

        // Update shelf count if shelf assigned
        if ($book->shelf_number) {
            $shelf = Shelf::where('code', $book->shelf_number)
                ->where('institution_id', $institution->id)
                ->first();
            if ($shelf) {
                $shelf->increment('current_count');
            }
        }

        // ==========================================
        // SEND NOTIFICATION
        // ==========================================
        LibraryNotificationHelper::bookAdded(
            $institution->id,
            $book,
            auth()->user()
        );

        return redirect()->route('librarian.books.index')
            ->with('success', 'Book created successfully!');
    }

    /**
     * Display the specified book.
     */
    public function show(Book $book)
    {
        $institution = auth()->user()->institution;

        if ($book->institution_id !== $institution->id) {
            abort(403, 'This book does not belong to your institution.');
        }

        return view('librarian.books.show', compact('book', 'institution'));
    }

    /**
     * Show the form for editing the specified book.
     */
    public function edit(Book $book)
    {
        $institution = auth()->user()->institution;

        if ($book->institution_id !== $institution->id) {
            abort(403, 'This book does not belong to your institution.');
        }

        $shelves = Shelf::where('institution_id', $institution->id)
            ->where('status', 'active')
            ->get();

        return view('librarian.books.edit', compact('book', 'institution', 'shelves'));
    }

    /**
     * Update the specified book.
     */
    public function update(Request $request, Book $book)
    {
        $institution = auth()->user()->institution;

        if ($book->institution_id !== $institution->id) {
            abort(403, 'This book does not belong to your institution.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'is_paid' => 'boolean',
            'price' => 'nullable|numeric|min:0',
            'status' => 'required|in:pending,approved,rejected',
            'shelf_number' => 'nullable|string|max:50|exists:shelves,code',
            'shelf_name' => 'nullable|string|max:100',
            'column_location' => 'nullable|string|max:100',
            'position' => 'nullable|string|max:100',
            'floor' => 'nullable|string|max:50',
            'section' => 'nullable|string|max:100',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'file' => 'nullable|file|mimes:pdf|max:10240',
            'total_pages' => 'nullable|integer|min:1',
        ]);

        // Prepare data
        $data = [
            'title' => $validated['title'],
            'author' => $validated['author'] ?? null,
            'category' => $validated['category'] ?? null,
            'description' => $validated['description'] ?? null,
            'is_paid' => $validated['is_paid'] ?? false,
            'price' => $validated['price'] ?? 0,
            'status' => $validated['status'] ?? 'pending',
            'shelf_number' => $validated['shelf_number'] ?? null,
            'shelf_name' => $validated['shelf_name'] ?? null,
            'column_location' => $validated['column_location'] ?? null,
            'position' => $validated['position'] ?? null,
            'floor' => $validated['floor'] ?? null,
            'section' => $validated['section'] ?? null,
            'total_pages' => $validated['total_pages'] ?? 0,
        ];

        // Handle cover image
        if ($request->hasFile('cover_image')) {
            if ($book->cover_image) {
                Storage::disk('public')->delete($book->cover_image);
            }
            $data['cover_image'] = $request->file('cover_image')->store('books/covers', 'public');
        }

        // Handle PDF file
        if ($request->hasFile('file')) {
            if ($book->file_path) {
                Storage::disk('public')->delete($book->file_path);
            }
            $data['file_path'] = $request->file('file')->store('books/pdfs', 'public');
        }

        // Update shelf count if shelf changed
        if ($book->shelf_number !== ($validated['shelf_number'] ?? null)) {
            // Decrease old shelf count
            if ($book->shelf_number) {
                $oldShelf = Shelf::where('code', $book->shelf_number)
                    ->where('institution_id', $institution->id)
                    ->first();
                if ($oldShelf) {
                    $oldShelf->decrement('current_count');
                }
            }
            // Increase new shelf count
            if ($validated['shelf_number'] ?? null) {
                $newShelf = Shelf::where('code', $validated['shelf_number'])
                    ->where('institution_id', $institution->id)
                    ->first();
                if ($newShelf) {
                    $newShelf->increment('current_count');
                }
            }
        }

        $book->update($data);

        return redirect()->route('librarian.books.index')
            ->with('success', 'Book updated successfully!');
    }

    /**
     * Remove the specified book.
     */
    public function destroy(Book $book)
    {
        $institution = auth()->user()->institution;

        if ($book->institution_id !== $institution->id) {
            abort(403, 'This book does not belong to your institution.');
        }

        // Decrease shelf count
        if ($book->shelf_number) {
            $shelf = Shelf::where('code', $book->shelf_number)
                ->where('institution_id', $institution->id)
                ->first();
            if ($shelf) {
                $shelf->decrement('current_count');
            }
        }

        // Delete files
        if ($book->cover_image) {
            Storage::disk('public')->delete($book->cover_image);
        }
        if ($book->file_path) {
            Storage::disk('public')->delete($book->file_path);
        }

        $book->delete();

        return redirect()->route('librarian.books.index')
            ->with('success', 'Book deleted successfully!');
    }

    /**
     * Approve a book.
     */
    public function approve(Book $book)
    {
        $institution = auth()->user()->institution;

        if ($book->institution_id !== $institution->id) {
            abort(403, 'This book does not belong to your institution.');
        }

        $book->update(['status' => 'approved']);

        // ==========================================
        // SEND NOTIFICATION
        // ==========================================
        LibraryNotificationHelper::bookApproved(
            $book->institution_id,
            $book,
            auth()->user()
        );

        return redirect()->back()
            ->with('success', 'Book approved successfully!');
    }

    /**
     * Reject a book.
     */
    public function reject(Request $request, Book $book)
    {
        $institution = auth()->user()->institution;

        if ($book->institution_id !== $institution->id) {
            abort(403, 'This book does not belong to your institution.');
        }

        $reason = $request->input('rejection_reason', 'No reason provided.');
        
        $book->update(['status' => 'rejected']);

        // ==========================================
        // SEND NOTIFICATION
        // ==========================================
        LibraryNotificationHelper::bookRejected(
            $book->institution_id,
            $book,
            $reason
        );

        return redirect()->back()
            ->with('success', 'Book rejected.');
    }
}