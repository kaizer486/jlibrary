<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Institution;
use Illuminate\Http\Request;

class InstitutionController extends Controller
{
    public function index(Request $request)
    {
        $query = Institution::withCount(['users', 'books']);
        
        // Search filter
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('city', 'like', '%' . $request->search . '%');
            });
        }
        
        // Status filter
        if ($request->status) {
            $query->where('status', $request->status);
        }
        
        // Type filter
        if ($request->type) {
            $query->where('type', $request->type);
        }
        
        $institutions = $query->latest()->paginate(15);
        
        // ==========================================
        // STATS - ADD ALL THESE VARIABLES
        // ==========================================
        $totalInstitutions = Institution::count();
        $pendingInstitutions = Institution::where('status', 'pending')->count();
        $approvedInstitutions = Institution::where('status', 'approved')->count();
        $suspendedInstitutions = Institution::where('status', 'suspended')->count();
        
        return view('admin.institutions.index', compact(
            'institutions',
            'totalInstitutions',
            'pendingInstitutions',
            'approvedInstitutions',
            'suspendedInstitutions'
        ));
    }

    public function create()
    {
        return view('admin.institutions.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string',
            'email' => 'required|email|unique:institutions,email',
            'phone' => 'nullable|string',
            'city' => 'nullable|string',
            'region' => 'nullable|string',
            'address' => 'nullable|string',
            'website' => 'nullable|url',
            'status' => 'required|in:pending,approved,suspended,inactive',
            'subscription_tier' => 'required|in:basic,premium,enterprise',
            'max_users' => 'nullable|integer|min:0',
            'max_books' => 'nullable|integer|min:0',
        ]);

        $institution = Institution::create($request->all());

        return redirect()->route('admin.institutions.index')
            ->with('success', 'Institution created successfully!');
    }

    public function show(Institution $institution)
    {
        $institution->loadCount(['users', 'books']);
        return view('admin.institutions.show', compact('institution'));
    }

    public function edit(Institution $institution)
    {
        return view('admin.institutions.edit', compact('institution'));
    }

    public function update(Request $request, Institution $institution)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string',
            'email' => 'required|email|unique:institutions,email,' . $institution->id,
            'phone' => 'nullable|string',
            'city' => 'nullable|string',
            'region' => 'nullable|string',
            'address' => 'nullable|string',
            'website' => 'nullable|url',
            'status' => 'required|in:pending,approved,suspended,inactive',
            'subscription_tier' => 'required|in:basic,premium,enterprise',
            'max_users' => 'nullable|integer|min:0',
            'max_books' => 'nullable|integer|min:0',
        ]);

        $institution->update($request->all());

        return redirect()->route('admin.institutions.index')
            ->with('success', 'Institution updated successfully!');
    }

    public function destroy(Institution $institution)
    {
        // Check if institution has users
        if ($institution->users()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete institution with users. Remove users first.');
        }

        $institution->delete();

        return redirect()->route('admin.institutions.index')
            ->with('success', 'Institution deleted successfully!');
    }

    public function approve(Institution $institution)
    {
        $institution->update(['status' => 'approved']);
        return redirect()->back()->with('success', 'Institution approved successfully!');
    }

    public function reject(Institution $institution)
    {
        $institution->update(['status' => 'inactive']);
        return redirect()->back()->with('success', 'Institution rejected successfully!');
    }

    public function suspend(Institution $institution)
    {
        $institution->update(['status' => 'suspended']);
        return redirect()->back()->with('success', 'Institution suspended successfully!');
    }
}