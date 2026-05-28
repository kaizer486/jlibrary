<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Institution;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;

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
        
        $institutions = $query->latest()->paginate(15);
        
        // Stats
        $totalInstitutions = Institution::count();
        $pendingInstitutions = Institution::where('status', 'pending')->count();
        $approvedInstitutions = Institution::where('status', 'approved')->count();
        
        return view('admin.institutions.index', compact('institutions', 'totalInstitutions', 'pendingInstitutions', 'approvedInstitutions'));
    }
    
public function show(Institution $institution)
{
    $institution->load(['users', 'approvedBy']);
    
    // Load books only if the relationship exists
    try {
        $institution->load(['books']);
    } catch (\Exception $e) {
        // Books table doesn't have institution_id yet, return empty collection
        $institution->setRelation('books', new Collection());
    }
    
    return view('admin.institutions.show', compact('institution'));
}
    
    public function create()
    {
        return view('admin.institutions.create');
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
            'status' => 'pending',
            'subscription_tier' => 'basic',
            'max_users' => 100,
            'max_books' => 1000,
        ]);
        
        return redirect()->route('admin.institutions.index')->with('success', 'Institution created successfully! Awaiting approval.');
    }
    
    public function edit(Institution $institution)
    {
        return view('admin.institutions.edit', compact('institution'));
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
            'status' => 'nullable|in:pending,approved,suspended,inactive',
            'subscription_tier' => 'nullable|in:basic,premium,enterprise',
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
        ]);
        
        // Only super admin can change these
        if (auth()->user()->isSuperAdmin()) {
            $institution->update([
                'status' => $request->status ?? $institution->status,
                'subscription_tier' => $request->subscription_tier ?? $institution->subscription_tier,
                'max_users' => $request->max_users ?? $institution->max_users,
                'max_books' => $request->max_books ?? $institution->max_books,
            ]);
        }
        
        return redirect()->route('admin.institutions.index')->with('success', 'Institution updated successfully!');
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
    
    public function reject(Institution $institution)
    {
        $institution->update([
            'status' => 'rejected',
        ]);
        
        return redirect()->back()->with('success', 'Institution rejected.');
    }
    
    public function suspend(Institution $institution)
    {
        $institution->update([
            'status' => 'suspended',
        ]);
        
        return redirect()->back()->with('success', 'Institution suspended.');
    }
    
    public function destroy(Institution $institution)
    {
        $institution->delete();
        return redirect()->route('admin.institutions.index')->with('success', 'Institution deleted successfully!');
    }
}