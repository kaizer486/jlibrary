<?php

namespace App\Http\Controllers\Institution;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookshopBook;
use App\Models\Shelf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\CertificateController;
use App\Models\Certificate;
use App\Mail\CertificateEarnedMail;
use Illuminate\Support\Facades\Mail;

class BookController extends Controller
{
   /**
 * Get the appropriate book model based on institution type.
 */
private function getBookModel()
{
    $institution = auth()->user()->institution;
    
    if ($institution && $institution->type === 'bookstore') {
        return new BookshopBook();
    }
    
    return new Book();
}

  /**
 * Get the book model class name.
 */
private function getBookModelClass()
{
    $institution = auth()->user()->institution;
    
    if ($institution && $institution->type === 'bookstore') {
        return BookshopBook::class;
    }
    
    return Book::class;
}

    /**
     * Display a listing of books.
     */
    public function index(Request $request)
    {
        $institution = auth()->user()->institution;

        if (!$institution) {
            abort(403, 'You do not belong to any institution.');
        }

        // ✅ Use the correct model based on institution type
        $bookModel = $this->getBookModelClass();
        $query = $bookModel::where('institution_id', $institution->id);

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

        // ✅ Get shelves (for filter dropdown)
        $shelves = Shelf::where('institution_id', $institution->id)
            ->where('status', 'active')
            ->get();

        $stats = [
            'total' => $bookModel::where('institution_id', $institution->id)->count(),
            'approved' => $bookModel::where('institution_id', $institution->id)->where('status', 'approved')->count(),
            'pending' => $bookModel::where('institution_id', $institution->id)->where('status', 'pending')->count(),
            'rejected' => $bookModel::where('institution_id', $institution->id)->where('status', 'rejected')->count(),
        ];

        $isBookstore = $institution->type === 'bookstore';

        // ✅ Use different view for bookstore
        if ($isBookstore) {
            return view('institution.books.index-bookstore', compact('books', 'stats', 'institution'));
        }

        return view('institution.books.index', compact('books', 'shelves', 'stats', 'institution'));
    }

  

/**
 * Show the form for creating a new book.
 */
public function create()
{
   
    $user = auth()->user();
    
    if (!$user) {
        abort(403, 'You need to be logged in.');
    }

    $institution = $user->institution;

    if (!$institution) {
        abort(403, 'You do not belong to any institution.');
    }

    $shelves = Shelf::where('institution_id', $institution->id)
        ->where('status', 'active')
        ->get();

    $isBookstore = $institution->type === 'bookstore';

    return view('institution.books.create', [
        'institution' => $institution,
        'shelves' => $shelves,
        'isBookstore' => $isBookstore
    ]);
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

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'is_paid' => 'boolean',
            'price' => 'nullable|numeric|min:0',
            'status' => 'required|in:pending,approved,rejected,active,inactive,out_of_stock',
            'shelf_number' => 'nullable|string|max:50',
            'shelf_name' => 'nullable|string|max:100',
            'column_location' => 'nullable|string|max:100',
            'position' => 'nullable|string|max:100',
            'floor' => 'nullable|string|max:50',
            'section' => 'nullable|string|max:100',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'file' => 'nullable|file|mimes:pdf|max:10240',
            'total_pages' => 'nullable|integer|min:1',
            'is_bookstore_item' => 'nullable|boolean',
            'book_type' => 'nullable|in:softcopy,hardcopy,both',
            'softcopy_price' => 'nullable|numeric|min:0',
            'hardcopy_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'nullable|integer|min:0',
            'hardcopy_available' => 'nullable|boolean',
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
            'is_bookstore_item' => $validated['is_bookstore_item'] ?? false,
            'book_type' => $validated['book_type'] ?? 'both',
            'softcopy_price' => $validated['softcopy_price'] ?? null,
            'hardcopy_price' => $validated['hardcopy_price'] ?? null,
            'stock_quantity' => $validated['stock_quantity'] ?? 0,
            'hardcopy_available' => $validated['hardcopy_available'] ?? false,
        ];

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('books/covers', 'public');
        }

        if ($request->hasFile('file')) {
            $data['file_path'] = $request->file('file')->store('books/pdfs', 'public');
        }

        // ✅ Use the correct model
        $bookModel = $this->getBookModelClass();
        $book = $bookModel::create($data);

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

  /**
 * Display the specified book.
 */
public function show($id)
{
    $institution = auth()->user()->institution;

    if (!$institution) {
        abort(403, 'You do not belong to any institution.');
    }

    // ✅ Use the correct model based on institution type
    $bookModel = $this->getBookModelClass();
    
    // ✅ Find the book - make sure we use the correct table
    $book = $bookModel::where('institution_id', $institution->id)
        ->where('id', $id)
        ->first();
    
    // ✅ If not found in the main table, try the other table
    if (!$book) {
        // Try the other model
        $otherModel = $institution->type === 'bookstore' ? Book::class : BookshopBook::class;
        $book = $otherModel::where('institution_id', $institution->id)
            ->where('id', $id)
            ->first();
    }
    
    // ✅ If still not found, abort
    if (!$book) {
        abort(404, 'Book not found in this institution.');
    }

    $isBookstore = $institution->type === 'bookstore';

    // ✅ Get related books (same category, excluding current book)
    $relatedBooks = $bookModel::where('institution_id', $institution->id)
        ->where('id', '!=', $book->id)
        ->where('category', $book->category)
        ->whereIn('status', ['approved', 'active'])
        ->limit(8)
        ->get();

    return view('institution.books.show', compact('book', 'institution', 'isBookstore', 'relatedBooks'));
}

  /**
 * Show the form for editing the specified book.
 */
public function edit($id)
{
    $institution = auth()->user()->institution;

    if (!$institution) {
        abort(403, 'You do not belong to any institution.');
    }

    // ✅ Use the correct model based on institution type
    $bookModel = $this->getBookModelClass();
    
    // ✅ Find the book
    $book = $bookModel::where('institution_id', $institution->id)
        ->where('id', $id)
        ->first();
    
    // ✅ If not found in the main table, try the other table
    if (!$book) {
        $otherModel = $institution->type === 'bookstore' ? Book::class : BookshopBook::class;
        $book = $otherModel::where('institution_id', $institution->id)
            ->where('id', $id)
            ->first();
    }
    
    // ✅ If still not found, abort
    if (!$book) {
        abort(404, 'Book not found in this institution.');
    }

    $isBookstore = $institution->type === 'bookstore';
    
    // ✅ ALWAYS fetch shelves, regardless of institution type
    $shelves = Shelf::where('institution_id', $institution->id)
        ->where('status', 'active')
        ->get();

    return view('institution.books.edit', compact('book', 'shelves', 'institution', 'isBookstore'));
}

    /**
     * Update the specified book in storage.
     */
    public function update(Request $request, $id)
    {
        $institution = auth()->user()->institution;

        if (!$institution) {
            abort(403, 'You do not belong to any institution.');
        }

        // ✅ Use the correct model
        $bookModel = $this->getBookModelClass();
        $book = $bookModel::where('institution_id', $institution->id)
            ->findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'is_paid' => 'boolean',
            'price' => 'nullable|numeric|min:0',
            'status' => 'required|in:pending,approved,rejected,active,inactive,out_of_stock',
            'shelf_number' => 'nullable|string|max:50',
            'shelf_name' => 'nullable|string|max:100',
            'column_location' => 'nullable|string|max:100',
            'position' => 'nullable|string|max:100',
            'floor' => 'nullable|string|max:50',
            'section' => 'nullable|string|max:100',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'file' => 'nullable|file|mimes:pdf|max:10240',
            'total_pages' => 'nullable|integer|min:1',
            'is_bookstore_item' => 'nullable|boolean',
            'book_type' => 'nullable|in:softcopy,hardcopy,both',
            'softcopy_price' => 'nullable|numeric|min:0',
            'hardcopy_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'nullable|integer|min:0',
        ]);

        $data = [
            'title' => $validated['title'],
            'author' => $validated['author'] ?? null,
            'category' => $validated['category'] ?? null,
            'description' => $validated['description'] ?? null,
            'is_paid' => $validated['is_paid'] ?? false,
            'price' => $validated['price'] ?? 0,
            'status' => $validated['status'],
            'shelf_number' => $validated['shelf_number'] ?? null,
            'shelf_name' => $validated['shelf_name'] ?? null,
            'column_location' => $validated['column_location'] ?? null,
            'position' => $validated['position'] ?? null,
            'floor' => $validated['floor'] ?? null,
            'section' => $validated['section'] ?? null,
            'total_pages' => $validated['total_pages'] ?? 0,
            'is_bookstore_item' => $validated['is_bookstore_item'] ?? false,
            'book_type' => $validated['book_type'] ?? 'both',
            'softcopy_price' => $validated['softcopy_price'] ?? null,
            'hardcopy_price' => $validated['hardcopy_price'] ?? null,
            'stock_quantity' => $validated['stock_quantity'] ?? 0,
        ];

        if ($request->hasFile('cover_image')) {
            // Delete old cover if exists
            if ($book->cover_image) {
                Storage::disk('public')->delete($book->cover_image);
            }
            $data['cover_image'] = $request->file('cover_image')->store('books/covers', 'public');
        }

        if ($request->hasFile('file')) {
            // Delete old file if exists
            if ($book->file_path) {
                Storage::disk('public')->delete($book->file_path);
            }
            $data['file_path'] = $request->file('file')->store('books/pdfs', 'public');
        }

        $book->update($data);

        // Update shelf count if shelf changed
        if ($book->shelf_number) {
            $shelf = Shelf::where('code', $book->shelf_number)
                ->where('institution_id', $institution->id)
                ->first();
            if ($shelf) {
                $shelf->increment('current_count');
            }
        }

        return redirect()->route('institution.books.index')
            ->with('success', 'Book updated successfully!');
    }

    /**
     * Remove the specified book from storage.
     */
    public function destroy($id)
    {
        $institution = auth()->user()->institution;

        if (!$institution) {
            abort(403, 'You do not belong to any institution.');
        }

        // ✅ Use the correct model
        $bookModel = $this->getBookModelClass();
        $book = $bookModel::where('institution_id', $institution->id)
            ->findOrFail($id);

        // Delete files
        if ($book->cover_image) {
            Storage::disk('public')->delete($book->cover_image);
        }
        if ($book->file_path) {
            Storage::disk('public')->delete($book->file_path);
        }

        // Decrement shelf count
        if ($book->shelf_number) {
            $shelf = Shelf::where('code', $book->shelf_number)
                ->where('institution_id', $institution->id)
                ->first();
            if ($shelf) {
                $shelf->decrement('current_count');
            }
        }

        $book->delete();

        return redirect()->route('institution.books.index')
            ->with('success', 'Book deleted successfully!');
    }

    /**
     * Approve a book (for librarians/institution admins).
     */
    public function approve($id)
    {
        $institution = auth()->user()->institution;

        if (!$institution) {
            abort(403, 'You do not belong to any institution.');
        }

        // ✅ Use the correct model
        $bookModel = $this->getBookModelClass();
        $book = $bookModel::where('institution_id', $institution->id)
            ->findOrFail($id);

        $book->update(['status' => 'approved']);

        return redirect()->route('institution.books.index')
            ->with('success', 'Book approved successfully!');
    }

    /**
     * Toggle stock status (for bookstore items).
     */
    public function toggleStock($id)
    {
        $institution = auth()->user()->institution;

        if (!$institution) {
            abort(403, 'You do not belong to any institution.');
        }

        // ✅ Use the correct model
        $bookModel = $this->getBookModelClass();
        $book = $bookModel::where('institution_id', $institution->id)
            ->findOrFail($id);

        $newStatus = $book->status === 'active' ? 'inactive' : 'active';
        $book->update(['status' => $newStatus]);

        return redirect()->route('institution.books.index')
            ->with('success', 'Book stock status updated successfully!');
    }

    /**
     * Update book reading progress and auto-generate certificate on completion.
     */
    public function updateProgress(Request $request, $bookId)
    {
        $book = Book::where('institution_id', auth()->user()->institution_id)
            ->findOrFail($bookId);
        
        $progress = $request->input('progress', 0);
        
        // Update or create pivot record
        $user = auth()->user();
        $user->books()->syncWithoutDetaching([
            $book->id => [
                'progress_percent' => $progress,
                'status' => $progress >= 100 ? 'completed' : 'reading',
                'last_read_at' => now(),
                'updated_at' => now(),
            ]
        ]);
        
        // Auto-generate certificate if book is completed (100%)
        if ($progress >= 100) {
            $existing = Certificate::where('user_id', $user->id)
                ->where('book_id', $book->id)
                ->first();
            
            if (!$existing) {
                // Generate certificate
                $certificateController = new CertificateController();
                $certificate = $certificateController->generateFromBook($book, 100);
                
                // Send email notification
                try {
                    Mail::to($user->email)->send(new CertificateEarnedMail(
                        $user->full_name,
                        $book->title,
                        100,
                        70
                    ));
                } catch (\Exception $e) {
                    \Log::error('Failed to send certificate email: ' . $e->getMessage());
                }
                
                return response()->json([
                    'success' => true,
                    'message' => '🎉 Congratulations! You earned a certificate for completing this book!',
                    'certificate_earned' => true,
                    'certificate_id' => $certificate->id,
                    'redirect' => route('certificates.show', $certificate)
                ]);
            }
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Progress updated successfully',
            'progress' => $progress
        ]);
    }

    /**
     * Bulk action for books (delete, approve, reject).
     */
    public function bulkAction(Request $request)
    {
        $institution = auth()->user()->institution;

        if (!$institution) {
            abort(403, 'You do not belong to any institution.');
        }

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:books,id',
            'action' => 'required|in:delete,approve,reject'
        ]);

        $bookModel = $this->getBookModelClass();
        $books = $bookModel::whereIn('id', $request->ids)
            ->where('institution_id', $institution->id)
            ->get();

        foreach ($books as $book) {
            if ($request->action === 'delete') {
                if ($book->cover_image) {
                    Storage::disk('public')->delete($book->cover_image);
                }
                if ($book->file_path) {
                    Storage::disk('public')->delete($book->file_path);
                }
                $book->delete();
            } else {
                $book->update(['status' => $request->action]);
            }
        }

        $message = $request->action === 'delete' 
            ? 'Selected books deleted successfully!' 
            : 'Selected books ' . $request->action . 'ed successfully!';

        return redirect()->route('institution.books.index')
            ->with('success', $message);
    }
}