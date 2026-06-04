<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Institution;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class InstitutionController extends Controller
{
    public function index(Request $request)
    {
        $query = Institution::query();
        
        // Search
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('city', 'like', '%' . $request->search . '%');
            });
        }
        
        // Filter by status
        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        
        // Filter by type
        if ($request->type && $request->type !== 'all') {
            $query->where('type', $request->type);
        }
        
        $institutions = $query->withCount('users')->latest()->paginate(15);
        
        // Stats
        $totalInstitutions = Institution::count();
        $pendingInstitutions = Institution::where('status', 'pending')->count();
        $approvedInstitutions = Institution::where('status', 'approved')->count();
        $suspendedInstitutions = Institution::where('status', 'suspended')->count();
        
        return view('super-admin.institutions.index', compact(
            'institutions', 'totalInstitutions', 'pendingInstitutions', 
            'approvedInstitutions', 'suspendedInstitutions'
        ));
    }
    
    public function create()
    {
        return view('super-admin.institutions.create');
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:institutions',
            'email' => 'required|email|unique:institutions',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'region' => 'nullable|string',
            'type' => 'required|in:school,college,university,library,bookstore,publisher,research_center,other',
            'website' => 'nullable|url',
            'status' => 'required|in:pending,approved,suspended,inactive',
            'subscription_tier' => 'required|in:basic,premium,enterprise',
            'max_users' => 'nullable|integer|min:1',
            'max_books' => 'nullable|integer|min:1',
        ]);
        
        $institution = Institution::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name) . '-' . uniqid(),
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'city' => $request->city,
            'region' => $request->region,
            'type' => $request->type,
            'website' => $request->website,
            'status' => $request->status,
            'subscription_tier' => $request->subscription_tier,
            'max_users' => $request->max_users ?? 100,
            'max_books' => $request->max_books ?? 1000,
            'approved_by' => auth()->id(),
            'approved_at' => $request->status === 'approved' ? now() : null,
        ]);
        
        // Create wallet for institution
        $institution->createWallet();
        
        return redirect()->route('super-admin.institutions.index')->with('success', 'Institution created successfully!');
    }
    
    public function show(Institution $institution)
    {
        $institution->load(['users', 'books', 'approvedBy']);
        $institution->loadCount(['users', 'books']);
        
        return view('super-admin.institutions.show', compact('institution'));
    }
    
    public function edit(Institution $institution)
    {
        return view('super-admin.institutions.edit', compact('institution'));
    }
    
    public function update(Request $request, Institution $institution)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:institutions,name,' . $institution->id,
            'email' => 'required|email|unique:institutions,email,' . $institution->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'region' => 'nullable|string',
            'type' => 'required|in:school,college,university,library,bookstore,publisher,research_center,other',
            'website' => 'nullable|url',
            'status' => 'required|in:pending,approved,suspended,inactive',
            'subscription_tier' => 'required|in:basic,premium,enterprise',
            'max_users' => 'nullable|integer|min:1',
            'max_books' => 'nullable|integer|min:1',
        ]);
        
        $institution->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'city' => $request->city,
            'region' => $request->region,
            'type' => $request->type,
            'website' => $request->website,
            'status' => $request->status,
            'subscription_tier' => $request->subscription_tier,
            'max_users' => $request->max_users ?? 100,
            'max_books' => $request->max_books ?? 1000,
            'approved_by' => $request->status === 'approved' ? auth()->id() : $institution->approved_by,
            'approved_at' => $request->status === 'approved' ? now() : $institution->approved_at,
        ]);
        
        return redirect()->route('super-admin.institutions.index')->with('success', 'Institution updated successfully!');
    }
    
    public function destroy(Institution $institution)
    {
        // Check if institution has users
        if ($institution->users()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete institution with associated users. Transfer or delete users first.');
        }
        
        $institution->delete();
        
        return redirect()->route('super-admin.institutions.index')->with('success', 'Institution deleted successfully!');
    }
    
    public function approve(Institution $institution)
    {
        $institution->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);
        
        return redirect()->back()->with('success', 'Institution approved successfully!');
    }
    
    public function suspend(Institution $institution)
    {
        $institution->update([
            'status' => 'suspended',
        ]);
        
        return redirect()->back()->with('success', 'Institution suspended successfully!');
    }
    
    public function reject(Institution $institution)
    {
        $institution->update([
            'status' => 'rejected',
        ]);
        
        return redirect()->back()->with('success', 'Institution rejected.');
    }
}