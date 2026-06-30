<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Institution;
use Illuminate\Http\Request;

class InstitutionController extends Controller
{
    /**
     * Display a listing of institutions (View-Only).
     */
    public function index(Request $request)
    {
        $query = Institution::withCount(['users', 'books']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('city', 'LIKE', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by type
        if ($request->filled('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }

        $institutions = $query->latest()->paginate(15)->appends($request->query());

        // Stats
        $stats = [
            'total' => Institution::count(),
            'approved' => Institution::where('status', 'approved')->count(),
            'pending' => Institution::where('status', 'pending')->count(),
            'suspended' => Institution::where('status', 'suspended')->count(),
        ];

        return view('admin.institutions.index', compact('institutions', 'stats'));
    }

    /**
     * Display the specified institution (View-Only).
     */
    public function show($id)
    {
        $institution = Institution::withCount(['users', 'books'])
            ->with(['wallet'])
            ->findOrFail($id);

        // Get recent users (only names, no sensitive data)
        $recentUsers = $institution->users()
            ->latest()
            ->limit(5)
            ->get(['id', 'full_name', 'email', 'role', 'created_at']);

        // Get recent books
        $recentBooks = $institution->books()
            ->latest()
            ->limit(5)
            ->get(['id', 'title', 'author', 'status', 'created_at']);

        return view('admin.institutions.show', compact('institution', 'recentUsers', 'recentBooks'));
    }

    /**
     * Show the form for creating a new institution (DISABLED for admin).
     */
    public function create()
    {
        abort(403, 'Admin does not have permission to create institutions.');
    }

    /**
     * Store a newly created institution (DISABLED for admin).
     */
    public function store(Request $request)
    {
        abort(403, 'Admin does not have permission to create institutions.');
    }

    /**
     * Show the form for editing the specified institution (DISABLED for admin).
     */
    public function edit($id)
    {
        abort(403, 'Admin does not have permission to edit institutions.');
    }

    /**
     * Update the specified institution (DISABLED for admin).
     */
    public function update(Request $request, $id)
    {
        abort(403, 'Admin does not have permission to update institutions.');
    }

    /**
     * Remove the specified institution (DISABLED for admin).
     */
    public function destroy($id)
    {
        abort(403, 'Admin does not have permission to delete institutions.');
    }
}