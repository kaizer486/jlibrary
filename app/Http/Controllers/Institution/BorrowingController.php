<?php

namespace App\Http\Controllers\Institution;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Borrowing;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BorrowingController extends Controller
{
    public function index(Request $request)
    {
        $institution = auth()->user()->institution;

        if (!$institution) {
            abort(403, 'You do not belong to any institution.');
        }

        $query = Borrowing::where('institution_id', $institution->id)
            ->with(['book', 'user']);

        if ($request->filled('status')) {
            if ($request->status === 'overdue') {
                $query->where('status', 'borrowed')
                    ->where('due_date', '<', now());
            } else {
                $query->where('status', $request->status);
            }
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('book', function($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%");
            });
        }

        $borrowings = $query->latest()->paginate(15)->appends($request->query());

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

        return view('institution.borrowings.index', compact('borrowings', 'members', 'stats'));
    }

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

        return view('institution.borrowings.create', compact('book', 'books', 'members'));
    }

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

        $book = Book::where('institution_id', $institution->id)
            ->where('status', 'approved')
            ->findOrFail($validated['book_id']);

        if ($book->isBorrowed()) {
            return redirect()->back()
                ->with('error', 'This book is already borrowed.')
                ->withInput();
        }

        $user = User::where('institution_id', $institution->id)
            ->findOrFail($validated['user_id']);

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

        $book->update(['availability' => 'borrowed']);

        return redirect()->route('institution.borrowings.index')
            ->with('success', "Book '{$book->title}' borrowed by {$user->full_name}.");
    }

    public function show(Borrowing $borrowing)
    {
        $institution = auth()->user()->institution;

        if ($borrowing->institution_id !== $institution->id) {
            abort(403, 'This borrowing does not belong to your institution.');
        }

        $borrowing->load(['book', 'user', 'borrowedBy', 'returnedTo']);

        return view('institution.borrowings.show', compact('borrowing'));
    }

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
            $borrowing->update([
                'status' => 'returned',
                'returned_at' => now(),
                'returned_to' => auth()->id(),
            ]);

            $borrowing->book->update(['availability' => 'available']);
        });

        return redirect()->route('institution.borrowings.index')
            ->with('success', "Book '{$borrowing->book->title}' returned successfully.");
    }

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
            $borrowing->book->update(['availability' => 'available']);
            $borrowing->delete();
        });

        return redirect()->route('institution.borrowings.index')
            ->with('success', 'Borrowing cancelled successfully.');
    }
}