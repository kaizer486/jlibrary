<?php

namespace App\Http\Controllers\Institution;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Shelf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $institution = auth()->user()->institution;

        if (!$institution) {
            abort(403, 'You do not belong to any institution.');
        }

        $query = Book::where('institution_id', $institution->id);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhere('author', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('shelf')) {
            $query->where('shelf_number', $request->shelf);
        }

        $books = $query->latest()->paginate(15)->appends($request->query());

        $shelves = Shelf::where('institution_id', $institution->id)
            ->where('status', 'active')
            ->get();

        $stats = [
            'total' => Book::where('institution_id', $institution->id)->count(),
            'approved' => Book::where('institution_id', $institution->id)->where('status', 'approved')->count(),
            'pending' => Book::where('institution_id', $institution->id)->where('status', 'pending')->count(),
            'rejected' => Book::where('institution_id', $institution->id)->where('status', 'rejected')->count(),
        ];

        return view('institution.books.index', compact('books', 'shelves', 'stats', 'institution'));
    }

    public function create()
    {
        $institution = auth()->user()->institution;

        if (!$institution) {
            abort(403, 'You do not belong to any institution.');
        }

        $shelves = Shelf::where('institution_id', $institution->id)
            ->where('status', 'active')
            ->get();

        return view('institution.books.create', compact('shelves', 'institution'));
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
            'category' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'is_paid' => 'boolean',
            'price' => 'nullable|numeric|min:0',
            'status' => 'required|in:pending,approved,rejected',
            'shelf_number' => 'nullable|string|max:50',
            'shelf_name' => 'nullable|string|max:100',
            'column_location' => 'nullable|string|max:100',
            'position' => 'nullable|string|max:100',
            'floor' => 'nullable|string|max:50',
            'section' => 'nullable|string|max:100',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'file' => 'nullable|file|mimes:pdf|max:10240',
            'total_pages' => 'nullable|integer|min:1',
        ]);

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

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('books/covers', 'public');
        }

        if ($request->hasFile('file')) {
            $data['file_path'] = $request->file('file')->store('books/pdfs', 'public');
        }

        $book = Book::create($data);

        if ($book->shelf_number) {
            $shelf = Shelf::where('code', $book->shelf_number)
                ->where('institution_id', $institution->id)
                ->first();
            if ($shelf) {
                $shelf->increment('current_count');
            }
        }

        return redirect()->route('institution.books.index')
            ->with('success', 'Book created successfully!');
    }

    public function show(Book $book)
    {
        $institution = auth()->user()->institution;

        if ($book->institution_id !== $institution->id) {
            abort(403, 'This book does not belong to your institution.');
        }

        return view('institution.books.show', compact('book', 'institution'));
    }

    public function edit(Book $book)
    {
        $institution = auth()->user()->institution;

        if ($book->institution_id !== $institution->id) {
            abort(403, 'This book does not belong to your institution.');
        }

        $shelves = Shelf::where('institution_id', $institution->id)
            ->where('status', 'active')
            ->get();

        return view('institution.books.edit', compact('book', 'shelves', 'institution'));
    }

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
            'shelf_number' => 'nullable|string|max:50',
            'shelf_name' => 'nullable|string|max:100',
            'column_location' => 'nullable|string|max:100',
            'position' => 'nullable|string|max:100',
            'floor' => 'nullable|string|max:50',
            'section' => 'nullable|string|max:100',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'file' => 'nullable|file|mimes:pdf|max:10240',
            'total_pages' => 'nullable|integer|min:1',
        ]);

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

        if ($request->hasFile('cover_image')) {
            if ($book->cover_image) {
                Storage::disk('public')->delete($book->cover_image);
            }
            $data['cover_image'] = $request->file('cover_image')->store('books/covers', 'public');
        }

        if ($request->hasFile('file')) {
            if ($book->file_path) {
                Storage::disk('public')->delete($book->file_path);
            }
            $data['file_path'] = $request->file('file')->store('books/pdfs', 'public');
        }

        if ($book->shelf_number !== ($validated['shelf_number'] ?? null)) {
            if ($book->shelf_number) {
                $oldShelf = Shelf::where('code', $book->shelf_number)
                    ->where('institution_id', $institution->id)
                    ->first();
                if ($oldShelf) {
                    $oldShelf->decrement('current_count');
                }
            }
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

        return redirect()->route('institution.books.index')
            ->with('success', 'Book updated successfully!');
    }

    public function destroy(Book $book)
    {
        $institution = auth()->user()->institution;

        if ($book->institution_id !== $institution->id) {
            abort(403, 'This book does not belong to your institution.');
        }

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
        if ($book->file_path) {
            Storage::disk('public')->delete($book->file_path);
        }

        $book->delete();

        return redirect()->route('institution.books.index')
            ->with('success', 'Book deleted successfully!');
    }

    public function approve(Book $book)
    {
        $institution = auth()->user()->institution;

        if ($book->institution_id !== $institution->id) {
            abort(403, 'This book does not belong to your institution.');
        }

        $book->update(['status' => 'approved']);

        return redirect()->back()->with('success', 'Book approved successfully!');
    }
}