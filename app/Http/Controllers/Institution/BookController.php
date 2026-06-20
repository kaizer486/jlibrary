<?php

namespace App\Http\Controllers\Institution;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Institution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class BookController extends Controller
{
    /**
     * Display a listing of books for the institution.
     */
    public function index(Request $request)
    {
        $institution = auth()->user()->institution;
        
        // Security check: user must belong to an institution
        if (!$institution) {
            abort(403, 'You do not belong to any institution.');
        }
        
        // Check if institution_id column exists (safety check)
        if (!Schema::hasColumn('books', 'institution_id')) {
            $books = collect();
            $stats = ['total' => 0, 'approved' => 0, 'pending' => 0, 'rejected' => 0];
            return view('institution.books.index', compact('books', 'institution', 'stats'))
                ->with('error', 'Books table is not properly configured. Please run migrations.');
        }
        
        // Build query - all good, column exists
        $query = Book::where('institution_id', $institution->id);
        
        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%");
                
                // Only use columns that exist
                if (Schema::hasColumn('books', 'author')) {
                    $q->orWhere('author', 'LIKE', "%{$search}%");
                }
                if (Schema::hasColumn('books', 'description')) {
                    $q->orWhere('description', 'LIKE', "%{$search}%");
                }
            });
        }
        
        // Status filter - only if column exists
        if ($request->filled('status') && Schema::hasColumn('books', 'status')) {
            $query->where('status', $request->status);
        }
        
        // Type filter (free/paid) - only if column exists
        if ($request->filled('type') && Schema::hasColumn('books', 'is_paid')) {
            if ($request->type === 'free') {
                $query->where('is_paid', false);
            } elseif ($request->type === 'paid') {
                $query->where('is_paid', true);
            }
        }
        
        // Get paginated results
        $books = $query->latest()->paginate($request->per_page ?? 15)
            ->appends($request->query());
        
        // Get statistics
        $stats = [
            'total' => Book::where('institution_id', $institution->id)->count(),
            'approved' => Schema::hasColumn('books', 'status') 
                ? Book::where('institution_id', $institution->id)->where('status', 'approved')->count() 
                : 0,
            'pending' => Schema::hasColumn('books', 'status') 
                ? Book::where('institution_id', $institution->id)->where('status', 'pending')->count() 
                : 0,
            'rejected' => Schema::hasColumn('books', 'status') 
                ? Book::where('institution_id', $institution->id)->where('status', 'rejected')->count() 
                : 0,
        ];
        
        return view('institution.books.index', compact('books', 'institution', 'stats'));
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
        
        if (!auth()->user()->can('create', Book::class)) {
            abort(403, 'You do not have permission to create books.');
        }
        
        if (!Schema::hasColumn('books', 'institution_id')) {
            return redirect()->route('institution.books.index')
                ->with('error', 'Books table is not properly configured. Please run migrations.');
        }
        
        return view('institution.books.create', compact('institution'));
    }
    
    /**
     * Store a newly created book in storage.
     */
    public function store(Request $request)
    {
        $institution = auth()->user()->institution;
        
        if (!$institution) {
            abort(403, 'You do not belong to any institution.');
        }
        
        if (!auth()->user()->can('create', Book::class)) {
            abort(403, 'You do not have permission to create books.');
        }
        
        if (!Schema::hasColumn('books', 'institution_id')) {
            return redirect()->route('institution.books.index')
                ->with('error', 'Books table is not properly configured.');
        }
        
        // Build validation rules based on existing columns
        $rules = [
            'title' => 'required|string|max:255',
        ];
        
        if (Schema::hasColumn('books', 'author')) {
            $rules['author'] = 'nullable|string|max:255';
        }
        
        if (Schema::hasColumn('books', 'description')) {
            $rules['description'] = 'nullable|string';
        }
        
        if (Schema::hasColumn('books', 'is_paid')) {
            $rules['is_paid'] = 'boolean';
        }
        
        if (Schema::hasColumn('books', 'price')) {
            $rules['price'] = 'nullable|numeric|min:0';
        }
        
        if (Schema::hasColumn('books', 'status')) {
            $rules['status'] = 'required|in:pending,approved,rejected';
        }
        
        if (Schema::hasColumn('books', 'cover_image')) {
            $rules['cover_image'] = 'nullable|image|mimes:jpeg,png,jpg|max:2048';
        }
        
        // file_path is used instead of pdf_path
        if (Schema::hasColumn('books', 'file_path')) {
            $rules['file'] = 'nullable|file|mimes:pdf|max:10240';
        }
        
        $validated = $request->validate($rules);
        
        // Prepare data
        $data = [
            'institution_id' => $institution->id,
            'title' => $validated['title'],
            'uploaded_by' => auth()->id(),
        ];
        
        // Only add fields that exist
        if (Schema::hasColumn('books', 'author') && isset($validated['author'])) {
            $data['author'] = $validated['author'];
        }
        
        if (Schema::hasColumn('books', 'description') && isset($validated['description'])) {
            $data['description'] = $validated['description'];
        }
        
        if (Schema::hasColumn('books', 'is_paid')) {
            $data['is_paid'] = $validated['is_paid'] ?? false;
        }
        
        if (Schema::hasColumn('books', 'price') && isset($validated['price'])) {
            $data['price'] = $validated['price'] ?? 0.00;
        }
        
        if (Schema::hasColumn('books', 'status')) {
            $data['status'] = $validated['status'] ?? 'pending';
        }
        
        // Handle cover image upload
        if (Schema::hasColumn('books', 'cover_image') && $request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('books/covers', 'public');
        }
        
        // Handle file upload (file_path column)
        if (Schema::hasColumn('books', 'file_path') && $request->hasFile('file')) {
            $data['file_path'] = $request->file('file')->store('books/pdfs', 'public');
        }
        
        // Create book
        $book = Book::create($data);
        
        return redirect()->route('institution.books.index')
            ->with('success', 'Book created successfully!');
    }
    
    /**
     * Display the specified book.
     */
    public function show(Book $book)
    {
        $institution = auth()->user()->institution;
        
        if (!$institution) {
            abort(403, 'You do not belong to any institution.');
        }
        
        if (!Schema::hasColumn('books', 'institution_id')) {
            abort(403, 'Books table is not properly configured.');
        }
        
        if ($book->institution_id !== $institution->id) {
            abort(403, 'This book does not belong to your institution.');
        }
        
        if (!auth()->user()->can('view', $book)) {
            abort(403, 'You do not have permission to view this book.');
        }
        
        return view('institution.books.show', compact('book', 'institution'));
    }
    
    /**
     * Show the form for editing the specified book.
     */
    public function edit(Book $book)
    {
        $institution = auth()->user()->institution;
        
        if (!$institution) {
            abort(403, 'You do not belong to any institution.');
        }
        
        if (!Schema::hasColumn('books', 'institution_id')) {
            abort(403, 'Books table is not properly configured.');
        }
        
        if ($book->institution_id !== $institution->id) {
            abort(403, 'This book does not belong to your institution.');
        }
        
        if (!auth()->user()->can('update', $book)) {
            abort(403, 'You do not have permission to edit this book.');
        }
        
        return view('institution.books.edit', compact('book', 'institution'));
    }
    
    /**
     * Update the specified book in storage.
     */
    public function update(Request $request, Book $book)
    {
        $institution = auth()->user()->institution;
        
        if (!$institution) {
            abort(403, 'You do not belong to any institution.');
        }
        
        if (!Schema::hasColumn('books', 'institution_id')) {
            abort(403, 'Books table is not properly configured.');
        }
        
        if ($book->institution_id !== $institution->id) {
            abort(403, 'This book does not belong to your institution.');
        }
        
        if (!auth()->user()->can('update', $book)) {
            abort(403, 'You do not have permission to update this book.');
        }
        
        // Build validation rules based on existing columns
        $rules = [
            'title' => 'required|string|max:255',
        ];
        
        if (Schema::hasColumn('books', 'author')) {
            $rules['author'] = 'nullable|string|max:255';
        }
        
        if (Schema::hasColumn('books', 'description')) {
            $rules['description'] = 'nullable|string';
        }
        
        if (Schema::hasColumn('books', 'is_paid')) {
            $rules['is_paid'] = 'boolean';
        }
        
        if (Schema::hasColumn('books', 'price')) {
            $rules['price'] = 'nullable|numeric|min:0';
        }
        
        if (Schema::hasColumn('books', 'status')) {
            $rules['status'] = 'required|in:pending,approved,rejected';
        }
        
        if (Schema::hasColumn('books', 'cover_image')) {
            $rules['cover_image'] = 'nullable|image|mimes:jpeg,png,jpg|max:2048';
        }
        
        if (Schema::hasColumn('books', 'file_path')) {
            $rules['file'] = 'nullable|file|mimes:pdf|max:10240';
        }
        
        $validated = $request->validate($rules);
        
        // Prepare data
        $data = [
            'title' => $validated['title'],
        ];
        
        // Only add fields that exist
        if (Schema::hasColumn('books', 'author') && isset($validated['author'])) {
            $data['author'] = $validated['author'];
        }
        
        if (Schema::hasColumn('books', 'description') && isset($validated['description'])) {
            $data['description'] = $validated['description'];
        }
        
        if (Schema::hasColumn('books', 'is_paid')) {
            $data['is_paid'] = $validated['is_paid'] ?? false;
        }
        
        if (Schema::hasColumn('books', 'price') && isset($validated['price'])) {
            $data['price'] = $validated['price'] ?? 0.00;
        }
        
        if (Schema::hasColumn('books', 'status')) {
            $data['status'] = $validated['status'] ?? 'pending';
        }
        
        // Handle cover image upload
        if (Schema::hasColumn('books', 'cover_image') && $request->hasFile('cover_image')) {
            if ($book->cover_image) {
                Storage::disk('public')->delete($book->cover_image);
            }
            $data['cover_image'] = $request->file('cover_image')->store('books/covers', 'public');
        }
        
        // Handle file upload (file_path column)
        if (Schema::hasColumn('books', 'file_path') && $request->hasFile('file')) {
            if ($book->file_path) {
                Storage::disk('public')->delete($book->file_path);
            }
            $data['file_path'] = $request->file('file')->store('books/pdfs', 'public');
        }
        
        // Update book
        $book->update($data);
        
        return redirect()->route('institution.books.index')
            ->with('success', 'Book updated successfully!');
    }
    
    /**
     * Remove the specified book from storage.
     */
    public function destroy(Book $book)
    {
        $institution = auth()->user()->institution;
        
        if (!$institution) {
            abort(403, 'You do not belong to any institution.');
        }
        
        if (!Schema::hasColumn('books', 'institution_id')) {
            abort(403, 'Books table is not properly configured.');
        }
        
        if ($book->institution_id !== $institution->id) {
            abort(403, 'This book does not belong to your institution.');
        }
        
        if (!auth()->user()->can('delete', $book)) {
            abort(403, 'You do not have permission to delete this book.');
        }
        
        // Delete files
        if (Schema::hasColumn('books', 'cover_image') && $book->cover_image) {
            Storage::disk('public')->delete($book->cover_image);
        }
        if (Schema::hasColumn('books', 'file_path') && $book->file_path) {
            Storage::disk('public')->delete($book->file_path);
        }
        
        $book->delete();
        
        return redirect()->route('institution.books.index')
            ->with('success', 'Book deleted successfully!');
    }
    
    /**
     * Approve a book (change status to approved).
     */
    public function approve(Book $book)
    {
        $institution = auth()->user()->institution;
        
        if (!$institution) {
            abort(403, 'You do not belong to any institution.');
        }
        
        if (!Schema::hasColumn('books', 'institution_id')) {
            return redirect()->back()
                ->with('error', 'Books table is not properly configured.');
        }
        
        if ($book->institution_id !== $institution->id) {
            abort(403, 'This book does not belong to your institution.');
        }
        
        if (!auth()->user()->can('update', $book)) {
            abort(403, 'You do not have permission to approve this book.');
        }
        
        if (!Schema::hasColumn('books', 'status')) {
            return redirect()->back()
                ->with('error', 'Status column does not exist in books table.');
        }
        
        $book->update(['status' => 'approved']);
        
        return redirect()->back()
            ->with('success', 'Book approved successfully!');
    }
    
    /**
     * Export books as CSV.
     */
    public function export(Request $request)
    {
        $institution = auth()->user()->institution;
        
        if (!$institution) {
            abort(403, 'You do not belong to any institution.');
        }
        
        if (!auth()->user()->can('export', Book::class)) {
            abort(403, 'You do not have permission to export books.');
        }
        
        if (!Schema::hasColumn('books', 'institution_id')) {
            return redirect()->back()
                ->with('error', 'Books table is not properly configured.');
        }
        
        $books = Book::where('institution_id', $institution->id)->get();
        
        $filename = "books_{$institution->id}_" . date('Y-m-d') . ".csv";
        $handle = fopen('php://output', 'w');
        
        // Headers - only use columns that exist
        $headers = ['ID', 'Title'];
        if (Schema::hasColumn('books', 'author')) $headers[] = 'Author';
        if (Schema::hasColumn('books', 'price')) $headers[] = 'Price';
        if (Schema::hasColumn('books', 'status')) $headers[] = 'Status';
        if (Schema::hasColumn('books', 'created_at')) $headers[] = 'Created At';
        
        fputcsv($handle, $headers);
        
        // Data
        foreach ($books as $book) {
            $row = [
                $book->id,
                $book->title,
            ];
            
            if (Schema::hasColumn('books', 'author')) $row[] = $book->author ?? 'N/A';
            if (Schema::hasColumn('books', 'price')) $row[] = $book->price ?? 0.00;
            if (Schema::hasColumn('books', 'status')) $row[] = $book->status ?? 'pending';
            if (Schema::hasColumn('books', 'created_at')) $row[] = $book->created_at->format('Y-m-d H:i:s');
            
            fputcsv($handle, $row);
        }
        
        fclose($handle);
        
        return response()->stream(
            function() use ($handle) {
                // Already output via fputcsv
            },
            200,
            [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename={$filename}",
            ]
        );
    }
}