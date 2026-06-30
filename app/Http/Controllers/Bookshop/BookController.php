<?php

namespace App\Http\Controllers\Bookshop;

use App\Http\Controllers\Controller;
use App\Models\BookshopBook;
use App\Models\Institution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    public function index()
    {
        $institution = auth()->user()->institution;
        
        if (!$institution) {
            abort(403, 'You do not belong to any institution.');
        }

        $books = BookshopBook::where('institution_id', $institution->id)
            ->latest()
            ->paginate(15);

        $stats = [
            'total' => BookshopBook::where('institution_id', $institution->id)->count(),
            'active' => BookshopBook::where('institution_id', $institution->id)->where('status', 'active')->count(),
            'out_of_stock' => BookshopBook::where('institution_id', $institution->id)->where('status', 'out_of_stock')->count(),
            'low_stock' => BookshopBook::where('institution_id', $institution->id)->where('stock_quantity', '<=', 5)->where('stock_quantity', '>', 0)->count(),
        ];

        return view('bookshop.books.index', compact('books', 'institution', 'stats'));
    }

    public function create()
    {
        $institution = auth()->user()->institution;
        
        if (!$institution) {
            abort(403, 'You do not belong to any institution.');
        }

        return view('bookshop.books.create', compact('institution'));
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
        ]);

        return redirect()->route('bookshop.books.index')
            ->with('success', 'Book added successfully!');
    }

    public function edit(BookshopBook $book)
    {
        $institution = auth()->user()->institution;
        
        if (!$institution || $book->institution_id !== $institution->id) {
            abort(403, 'You do not have access to this book.');
        }

        return view('bookshop.books.edit', compact('book', 'institution'));
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
        ]);

        $data = $validated;

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
}