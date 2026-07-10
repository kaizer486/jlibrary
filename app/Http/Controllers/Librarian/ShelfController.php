<?php

namespace App\Http\Controllers\Librarian;

use App\Http\Controllers\Controller;
use App\Models\Shelf;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ShelfController extends Controller
{
    public function index(Request $request)
    {
        $institution = auth()->user()->institution;
        
        if (!$institution) {
            abort(403, 'You do not belong to any institution.');
        }

        $query = Shelf::where('institution_id', $institution->id);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('code', 'LIKE', "%{$search}%")
                  ->orWhere('category', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $shelves = $query->orderBy('code')->paginate(12)->appends($request->query());

        $stats = [
            'total' => Shelf::where('institution_id', $institution->id)->count(),
            'active' => Shelf::where('institution_id', $institution->id)->where('status', 'active')->count(),
            'full' => Shelf::where('institution_id', $institution->id)->where('status', 'full')->count(),
            'inactive' => Shelf::where('institution_id', $institution->id)->where('status', 'inactive')->count(),
        ];

        return view('librarian.shelves.index', compact('shelves', 'stats'));
    }

    public function create()
    {
        return view('librarian.shelves.create');
    }

    public function store(Request $request)
{
    $institution = auth()->user()->institution;

    if (!$institution) {
        abort(403, 'You do not belong to any institution.');
    }

    // ✅ Check only NON-DELETED records
    $exists = Shelf::where('institution_id', $institution->id)
        ->where('code', $request->code)
        ->whereNull('deleted_at')  // ✅ Only check non-deleted
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

    return redirect()->route('librarian.shelves.index')
        ->with('success', 'Shelf created successfully!');
}
    public function show(Shelf $shelf)
    {
        $institution = auth()->user()->institution;
        
        if ($shelf->institution_id !== $institution->id) {
            abort(403, 'This shelf does not belong to your institution.');
        }

        $books = $shelf->books()->where('institution_id', $institution->id)->get();
        $percentage = $shelf->capacity > 0 ? round(($shelf->current_count / $shelf->capacity) * 100) : 0;

        return view('librarian.shelves.show', compact('shelf', 'books', 'percentage'));
    }

    public function edit(Shelf $shelf)
    {
        $institution = auth()->user()->institution;
        
        if ($shelf->institution_id !== $institution->id) {
            abort(403, 'This shelf does not belong to your institution.');
        }

        return view('librarian.shelves.edit', compact('shelf'));
    }

    public function update(Request $request, Shelf $shelf)
    {
        $institution = auth()->user()->institution;
        
        if ($shelf->institution_id !== $institution->id) {
            abort(403, 'This shelf does not belong to your institution.');
        }

        // ✅ Manual check first (excluding current shelf)
        $exists = Shelf::where('institution_id', $institution->id)
            ->where('code', $request->code)
            ->where('id', '!=', $shelf->id)
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

        $shelf->update($validated);

        return redirect()->route('librarian.shelves.index')
            ->with('success', 'Shelf updated successfully!');
    }

    public function destroy(Shelf $shelf)
    {
        $institution = auth()->user()->institution;
        
        if ($shelf->institution_id !== $institution->id) {
            abort(403, 'This shelf does not belong to your institution.');
        }

        if ($shelf->books()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete shelf with books. Remove books first.');
        }

        $shelf->delete();

        return redirect()->route('librarian.shelves.index')
            ->with('success', 'Shelf deleted successfully!');
    }
}