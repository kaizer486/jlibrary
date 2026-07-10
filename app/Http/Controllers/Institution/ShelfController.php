<?php

namespace App\Http\Controllers\Institution;

use App\Http\Controllers\Controller;
use App\Models\Shelf;
use App\Models\Book;
use App\Models\BookshopBook;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ShelfController extends Controller
{
    /**
     * Display a listing of shelves.
     */
    public function index(Request $request)
    {
        $institution = auth()->user()->institution;

        if (!$institution) {
            abort(403, 'You do not belong to any institution.');
        }

        $query = Shelf::where('institution_id', $institution->id);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('code', 'LIKE', "%{$search}%")
                  ->orWhere('category', 'LIKE', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $shelves = $query->orderBy('code')->paginate(12)->appends($request->query());

        // ✅ Get the correct book model
        $bookModel = $institution->type === 'bookstore' ? BookshopBook::class : Book::class;
        $statusCondition = $institution->type === 'bookstore' ? 'active' : 'approved';

        // ✅ Calculate stats
        $stats = [
            'total' => Shelf::where('institution_id', $institution->id)->count(),
            'active' => Shelf::where('institution_id', $institution->id)->where('status', 'active')->count(),
            'full' => Shelf::where('institution_id', $institution->id)->where('status', 'full')->count(),
            'inactive' => Shelf::where('institution_id', $institution->id)->where('status', 'inactive')->count(),
        ];

        // ✅ Add book count to each shelf
        foreach ($shelves as $shelf) {
            $shelf->book_count = $bookModel::where('institution_id', $institution->id)
                ->where('shelf_number', $shelf->code)
                ->where('status', $statusCondition)
                ->count();
        }

        return view('institution.shelves.index', compact('shelves', 'stats'));
    }

    /**
     * Show the form for creating a new shelf.
     */
    public function create()
    {
        $institution = auth()->user()->institution;

        if (!$institution) {
            abort(403, 'You do not belong to any institution.');
        }

        return view('institution.shelves.create', compact('institution'));
    }

    /**
     * Store a newly created shelf.
     */
    public function store(Request $request)
    {
        $institution = auth()->user()->institution;

        if (!$institution) {
            abort(403, 'You do not belong to any institution.');
        }

        // ✅ Check if code exists in this institution (excluding soft-deleted)
        $exists = Shelf::where('institution_id', $institution->id)
            ->where('code', $request->code)
            ->whereNull('deleted_at')
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->with('error', 'The shelf code "' . $request->code . '" has already been taken in this institution.')
                ->withInput();
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50',
            'category' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'floor' => 'nullable|string|max:50',
            'section' => 'nullable|string|max:100',
            'column' => 'nullable|string|max:50',
            'row' => 'nullable|string|max:50',
            'capacity' => 'required|integer|min:1',
            'current_count' => 'nullable|integer|min:0',
            'status' => 'required|in:active,inactive,full',
        ]);

        $validated['institution_id'] = $institution->id;
        $validated['current_count'] = $validated['current_count'] ?? 0;

        Shelf::create($validated);

        return redirect()->route('institution.shelves.index')
            ->with('success', 'Shelf created successfully!');
    }

    /**
     * Display the specified shelf.
     */
    public function show(Shelf $shelf)
    {
        $institution = auth()->user()->institution;

        if ($shelf->institution_id !== $institution->id) {
            abort(403, 'This shelf does not belong to your institution.');
        }

        // ✅ Determine which model to use
        $bookModel = $institution->type === 'bookstore' ? BookshopBook::class : Book::class;
        $statusCondition = $institution->type === 'bookstore' ? 'active' : 'approved';

        // ✅ Get books on this shelf using the correct model
        $books = $bookModel::where('institution_id', $institution->id)
            ->where('shelf_number', $shelf->code)
            ->where('status', $statusCondition)
            ->get();

        $percentage = $shelf->capacity > 0 ? round(($shelf->current_count / $shelf->capacity) * 100) : 0;

        return view('institution.shelves.show', compact('shelf', 'books', 'percentage'));
    }

    /**
     * Show the form for editing the specified shelf.
     */
    public function edit(Shelf $shelf)
    {
        $institution = auth()->user()->institution;

        if ($shelf->institution_id !== $institution->id) {
            abort(403, 'This shelf does not belong to your institution.');
        }

        return view('institution.shelves.edit', compact('shelf'));
    }

    /**
     * Update the specified shelf.
     */
    public function update(Request $request, Shelf $shelf)
    {
        $institution = auth()->user()->institution;

        if ($shelf->institution_id !== $institution->id) {
            abort(403, 'This shelf does not belong to your institution.');
        }

        // ✅ Check if code exists in this institution (excluding current shelf and soft-deleted)
        $exists = Shelf::where('institution_id', $institution->id)
            ->where('code', $request->code)
            ->where('id', '!=', $shelf->id)
            ->whereNull('deleted_at')
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->with('error', 'The shelf code "' . $request->code . '" has already been taken in this institution.')
                ->withInput();
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50',
            'category' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'floor' => 'nullable|string|max:50',
            'section' => 'nullable|string|max:100',
            'column' => 'nullable|string|max:50',
            'row' => 'nullable|string|max:50',
            'capacity' => 'required|integer|min:1',
            'current_count' => 'nullable|integer|min:0',
            'status' => 'required|in:active,inactive,full',
        ]);

        $validated['current_count'] = $validated['current_count'] ?? 0;

        // ✅ Update shelf count if capacity changed
        if ($shelf->capacity != $validated['capacity'] && $shelf->current_count > $validated['capacity']) {
            $validated['current_count'] = $validated['capacity'];
            $validated['status'] = 'full';
        }

        $shelf->update($validated);

        return redirect()->route('institution.shelves.index')
            ->with('success', 'Shelf updated successfully!');
    }

    /**
     * Remove the specified shelf.
     */
    public function destroy(Shelf $shelf)
    {
        $institution = auth()->user()->institution;

        if ($shelf->institution_id !== $institution->id) {
            abort(403, 'This shelf does not belong to your institution.');
        }

        // ✅ Determine which model to use for book check
        $bookModel = $institution->type === 'bookstore' ? BookshopBook::class : Book::class;
        $statusCondition = $institution->type === 'bookstore' ? 'active' : 'approved';

        // ✅ Check if shelf has books
        $bookCount = $bookModel::where('institution_id', $institution->id)
            ->where('shelf_number', $shelf->code)
            ->where('status', $statusCondition)
            ->count();

        if ($bookCount > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete shelf with ' . $bookCount . ' books. Remove books first.');
        }

        $shelf->delete();

        return redirect()->route('institution.shelves.index')
            ->with('success', 'Shelf deleted successfully!');
    }

    /**
     * Check if a shelf code exists in an institution (excluding soft-deleted)
     */
    private function shelfCodeExists($institutionId, $code, $excludeId = null)
    {
        $query = Shelf::where('institution_id', $institutionId)
            ->where('code', $code)
            ->whereNull('deleted_at');
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        
        return $query->exists();
    }

    /**
     * Sync shelf counts with actual book counts.
     */
    public function syncCounts()
    {
        $institution = auth()->user()->institution;

        if (!$institution) {
            abort(403, 'You do not belong to any institution.');
        }

        // ✅ Determine which model to use
        $bookModel = $institution->type === 'bookstore' ? BookshopBook::class : Book::class;
        $statusCondition = $institution->type === 'bookstore' ? 'active' : 'approved';

        $shelves = Shelf::where('institution_id', $institution->id)->get();
        $updated = 0;

        foreach ($shelves as $shelf) {
            $count = $bookModel::where('institution_id', $institution->id)
                ->where('shelf_number', $shelf->code)
                ->where('status', $statusCondition)
                ->count();

            if ($shelf->current_count != $count) {
                $shelf->current_count = $count;
                
                // Update status based on capacity
                if ($count >= $shelf->capacity && $shelf->capacity > 0) {
                    $shelf->status = 'full';
                } elseif ($shelf->status == 'full') {
                    $shelf->status = 'active';
                }
                
                $shelf->save();
                $updated++;
            }
        }

        return redirect()->route('institution.shelves.index')
            ->with('success', "Synced {$updated} shelves successfully!");
    }
}