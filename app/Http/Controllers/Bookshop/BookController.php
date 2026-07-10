<?php

namespace App\Http\Controllers\Bookshop;

use App\Http\Controllers\Controller;
use App\Models\BookshopBook;
use App\Models\Institution;
use App\Models\Shelf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $institution = auth()->user()->institution;
        
        if (!$institution) {
            abort(403, 'You do not belong to any institution.');
        }

        $query = BookshopBook::where('institution_id', $institution->id);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhere('author', 'LIKE', "%{$search}%")
                  ->orWhere('category', 'LIKE', "%{$search}%");
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

        $books = $query->latest()->paginate(15);

        // ✅ Get shelves for filter
        $shelves = Shelf::where('institution_id', $institution->id)
            ->where('status', 'active')
            ->get();

        $stats = [
            'total' => BookshopBook::where('institution_id', $institution->id)->count(),
            'active' => BookshopBook::where('institution_id', $institution->id)->where('status', 'active')->count(),
            'out_of_stock' => BookshopBook::where('institution_id', $institution->id)->where('status', 'out_of_stock')->count(),
            'low_stock' => BookshopBook::where('institution_id', $institution->id)->where('stock_quantity', '<=', 5)->where('stock_quantity', '>', 0)->count(),
        ];

        return view('bookshop.books.index', compact('books', 'institution', 'stats', 'shelves'));
    }

    public function create()
    {
        $institution = auth()->user()->institution;
        
        if (!$institution) {
            abort(403, 'You do not belong to any institution.');
        }

        // ✅ Get shelves for the dropdown
        $shelves = Shelf::where('institution_id', $institution->id)
            ->where('status', 'active')
            ->get();

        return view('bookshop.books.create', compact('institution', 'shelves'));
    }

    public function store(Request $request)
    {
        $institution = auth()->user()->institution;
        
        if (!$institution) {
            abort(403, 'You do not belong to any institution.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'status' => 'required|in:active,inactive,out_of_stock',
            'category' => 'nullable|string|max:100',
            'isbn' => 'nullable|string|max:20',
            'pages' => 'nullable|integer|min:1',
            'publisher' => 'nullable|string|max:255',
            'publication_year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'shelf_number' => 'nullable|string|max:50', // ✅ Add shelf_number validation
        ]);

        $coverPath = null;
        if ($request->hasFile('cover_image')) {
            $coverPath = $request->file('cover_image')->store('bookshop/covers', 'public');
        }

        $book = BookshopBook::create([
            'institution_id' => $institution->id,
            'title' => $validated['title'],
            'author' => $validated['author'] ?? null,
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'stock_quantity' => $validated['stock_quantity'],
            'status' => $validated['status'],
            'category' => $validated['category'] ?? null,
            'isbn' => $validated['isbn'] ?? null,
            'pages' => $validated['pages'] ?? null,
            'publisher' => $validated['publisher'] ?? null,
            'publication_year' => $validated['publication_year'] ?? null,
            'cover_image' => $coverPath,
            'shelf_number' => $validated['shelf_number'] ?? null, // ✅ Save shelf_number
        ]);

        // ✅ Update shelf count
        if ($book->shelf_number) {
            $shelf = Shelf::where('code', $book->shelf_number)
                ->where('institution_id', $institution->id)
                ->first();
            if ($shelf) {
                $shelf->increment('current_count');
            }
        }

        return redirect()->route('bookshop.books.index')
            ->with('success', 'Book added successfully!');
    }

    public function edit(BookshopBook $book)
    {
        $institution = auth()->user()->institution;
        
        if (!$institution || $book->institution_id !== $institution->id) {
            abort(403, 'You do not have access to this book.');
        }

        // ✅ Get shelves for the dropdown
        $shelves = Shelf::where('institution_id', $institution->id)
            ->where('status', 'active')
            ->get();

        return view('bookshop.books.edit', compact('book', 'institution', 'shelves'));
    }

    public function update(Request $request, BookshopBook $book)
    {
        $institution = auth()->user()->institution;
        
        if (!$institution || $book->institution_id !== $institution->id) {
            abort(403, 'You do not have access to this book.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'status' => 'required|in:active,inactive,out_of_stock',
            'category' => 'nullable|string|max:100',
            'isbn' => 'nullable|string|max:20',
            'pages' => 'nullable|integer|min:1',
            'publisher' => 'nullable|string|max:255',
            'publication_year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'shelf_number' => 'nullable|string|max:50', // ✅ Add shelf_number validation
        ]);

        $data = $validated;

        // ✅ Handle shelf count changes
        $oldShelfNumber = $book->shelf_number;
        $newShelfNumber = $validated['shelf_number'] ?? null;

        if ($oldShelfNumber != $newShelfNumber) {
            // Decrement old shelf
            if ($oldShelfNumber) {
                $oldShelf = Shelf::where('code', $oldShelfNumber)
                    ->where('institution_id', $institution->id)
                    ->first();
                if ($oldShelf) {
                    $oldShelf->decrement('current_count');
                }
            }

            // Increment new shelf
            if ($newShelfNumber) {
                $newShelf = Shelf::where('code', $newShelfNumber)
                    ->where('institution_id', $institution->id)
                    ->first();
                if ($newShelf) {
                    $newShelf->increment('current_count');
                }
            }
        }

        if ($request->hasFile('cover_image')) {
            if ($book->cover_image) {
                Storage::disk('public')->delete($book->cover_image);
            }
            $data['cover_image'] = $request->file('cover_image')->store('bookshop/covers', 'public');
        }

        $book->update($data);

        return redirect()->route('bookshop.books.index')
            ->with('success', 'Book updated successfully!');
    }

    public function destroy(BookshopBook $book)
    {
        $institution = auth()->user()->institution;
        
        if (!$institution || $book->institution_id !== $institution->id) {
            abort(403, 'You do not have access to this book.');
        }

        // ✅ Decrement shelf count
        if ($book->shelf_number) {
            $shelf = Shelf::where('code', $book->shelf_number)
                ->where('institution_id', $institution->id)
                ->first();
            if ($shelf) {
                $shelf->decrement('current_count');
            }
        }

        if ($book->cover_image) {
            Storage::disk('public')->delete($book->cover_image);
        }

        $book->delete();

        return redirect()->route('bookshop.books.index')
            ->with('success', 'Book deleted successfully!');
    }

    public function show(BookshopBook $book)
    {
        $institution = auth()->user()->institution;
        
        if (!$institution || $book->institution_id !== $institution->id) {
            abort(403, 'You do not have access to this book.');
        }

        return view('bookshop.books.show', compact('book', 'institution'));
    }

    /**
     * Update stock quantity (AJAX).
     */
    public function updateStock(Request $request, BookshopBook $book)
    {
        $institution = auth()->user()->institution;
        
        if (!$institution || $book->institution_id !== $institution->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'stock_quantity' => 'required|integer|min:0',
        ]);

        $book->update([
            'stock_quantity' => $request->stock_quantity,
            'status' => $request->stock_quantity > 0 ? 'active' : 'out_of_stock'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Stock updated successfully!',
            'stock_quantity' => $book->stock_quantity,
            'status' => $book->status
        ]);
    }

    /**
     * Bulk update status.
     */
    public function bulkStatusUpdate(Request $request)
    {
        $institution = auth()->user()->institution;
        
        if (!$institution) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'book_ids' => 'required|array',
            'book_ids.*' => 'exists:bookshop_books,id',
            'status' => 'required|in:active,inactive,out_of_stock'
        ]);

        $books = BookshopBook::whereIn('id', $request->book_ids)
            ->where('institution_id', $institution->id)
            ->get();

        foreach ($books as $book) {
            $book->update(['status' => $request->status]);
        }

        return response()->json([
            'success' => true,
            'message' => count($books) . ' books updated successfully!'
        ]);
    }
}