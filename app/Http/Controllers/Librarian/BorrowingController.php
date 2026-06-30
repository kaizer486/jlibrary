<?php

namespace App\Http\Controllers\Librarian;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Borrowing;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BorrowingController extends Controller
{
    /**
     * Display a listing of borrowings.
     */
    public function index(Request $request)
    {
        $institution = auth()->user()->institution;

        if (!$institution) {
            abort(403, 'You do not belong to any institution.');
        }

        $query = Borrowing::where('institution_id', $institution->id)
            ->with(['book', 'user']);

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'overdue') {
                $query->where('status', 'borrowed')
                    ->where('due_date', '<', now());
            } else {
                $query->where('status', $request->status);
            }
        }

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Search by book title
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('book', function($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%");
            });
        }

        $borrowings = $query->latest()->paginate(15)->appends($request->query());

        // Get members for filter dropdown
        $members = User::where('institution_id', $institution->id)
            ->orderBy('full_name')
            ->get(['id', 'full_name']);

        $stats = [
            'total' => Borrowing::where('institution_id', $institution->id)->count(),
            'active' => Borrowing::where('institution_id', $institution->id)->where('status', 'borrowed')->count(),
            'overdue' => Borrowing::where('institution_id', $institution->id)
                ->where('status', 'borrowed')
                ->where('due_date', '<', now())
                ->count(),
            'returned' => Borrowing::where('institution_id', $institution->id)->where('status', 'returned')->count(),
        ];

        return view('librarian.borrowings.index', compact('borrowings', 'members', 'stats'));
    }

    /**
     * Show form to borrow a book.
     */
    public function create(Request $request)
    {
        $institution = auth()->user()->institution;

        if (!$institution) {
            abort(403, 'You do not belong to any institution.');
        }

        $bookId = $request->book_id;

        if ($bookId) {
            $book = Book::where('institution_id', $institution->id)
                ->where('status', 'approved')
                ->findOrFail($bookId);

            // Check if book is already borrowed
            if ($book->isBorrowed()) {
                return redirect()->back()->with('error', 'This book is already borrowed.');
            }
        } else {
            $book = null;
        }

        $books = Book::where('institution_id', $institution->id)
            ->where('status', 'approved')
            ->whereDoesntHave('activeBorrowings')
            ->orderBy('title')
            ->get(['id', 'title', 'author']);

        $members = User::where('institution_id', $institution->id)
            ->orderBy('full_name')
            ->get(['id', 'full_name', 'email']);

        return view('librarian.borrowings.create', compact('book', 'books', 'members'));
    }

    /**
     * Store a new borrowing.
     */
    public function store(Request $request)
    {
        $institution = auth()->user()->institution;

        if (!$institution) {
            abort(403, 'You do not belong to any institution.');
        }

        $validated = $request->validate([
            'book_id' => 'required|exists:books,id',
            'user_id' => 'required|exists:users,id',
            'due_date' => 'required|date|after:today',
            'notes' => 'nullable|string',
        ]);

        // Check if book exists in this institution
        $book = Book::where('institution_id', $institution->id)
            ->where('status', 'approved')
            ->findOrFail($validated['book_id']);

        // Check if book is already borrowed
        if ($book->isBorrowed()) {
            return redirect()->back()
                ->with('error', 'This book is already borrowed.')
                ->withInput();
        }

        // Check if user belongs to this institution
        $user = User::where('institution_id', $institution->id)
            ->findOrFail($validated['user_id']);

        // Create borrowing
        $borrowing = Borrowing::create([
            'book_id' => $book->id,
            'user_id' => $user->id,
            'institution_id' => $institution->id,
            'borrowed_at' => now(),
            'due_date' => $validated['due_date'],
            'status' => 'borrowed',
            'notes' => $validated['notes'] ?? null,
            'borrowed_by' => auth()->id(),
        ]);

        // Update book availability
        $book->update(['availability' => 'borrowed']);

        return redirect()->route('librarian.borrowings.index')
            ->with('success', "Book '{$book->title}' borrowed by {$user->full_name}.");
    }

    /**
     * Return a borrowed book.
     */
    public function returnBook(Borrowing $borrowing)
    {
        $institution = auth()->user()->institution;

        if ($borrowing->institution_id !== $institution->id) {
            abort(403, 'This borrowing does not belong to your institution.');
        }

        if ($borrowing->isReturned()) {
            return redirect()->back()->with('error', 'This book has already been returned.');
        }

        DB::transaction(function () use ($borrowing) {
            // Update borrowing
            $borrowing->update([
                'status' => 'returned',
                'returned_at' => now(),
                'returned_to' => auth()->id(),
            ]);

            // Update book availability
            $borrowing->book->update(['availability' => 'available']);
        });

        return redirect()->route('librarian.borrowings.index')
            ->with('success', "Book '{$borrowing->book->title}' returned successfully.");
    }

    /**
     * Show borrowing details.
     */
    public function show(Borrowing $borrowing)
    {
        $institution = auth()->user()->institution;

        if ($borrowing->institution_id !== $institution->id) {
            abort(403, 'This borrowing does not belong to your institution.');
        }

        $borrowing->load(['book', 'user', 'borrowedBy', 'returnedTo']);

        return view('librarian.borrowings.show', compact('borrowing'));
    }

    /**
     * Cancel/delete a borrowing.
     */
    public function destroy(Borrowing $borrowing)
    {
        $institution = auth()->user()->institution;

        if ($borrowing->institution_id !== $institution->id) {
            abort(403, 'This borrowing does not belong to your institution.');
        }

        if ($borrowing->isReturned()) {
            return redirect()->back()->with('error', 'Cannot delete a returned borrowing.');
        }

        DB::transaction(function () use ($borrowing) {
            // Update book availability
            $borrowing->book->update(['availability' => 'available']);

            // Delete borrowing
            $borrowing->delete();
        });

        return redirect()->route('librarian.borrowings.index')
            ->with('success', 'Borrowing cancelled successfully.');
    }

    /**
     * My Borrowings (User view).
     */
    public function myBorrowings()
    {
        $user = auth()->user();

        $borrowings = Borrowing::where('user_id', $user->id)
            ->with(['book', 'book.institution'])
            ->latest()
            ->paginate(15);

        $activeCount = Borrowing::where('user_id', $user->id)
            ->where('status', 'borrowed')
            ->count();

        $overdueCount = Borrowing::where('user_id', $user->id)
            ->where('status', 'borrowed')
            ->where('due_date', '<', now())
            ->count();

        return view('user.borrowings.index', compact('borrowings', 'activeCount', 'overdueCount'));
    }
}