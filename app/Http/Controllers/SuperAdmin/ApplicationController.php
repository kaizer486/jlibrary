<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ApplicationController extends Controller
{
    public function index(Request $request)
    {
        $query = Application::with(['user', 'reviewer']);
        
        // Search
        if ($request->search) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('full_name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
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
        
        $applications = $query->latest()->paginate(15);
        
        $stats = [
            'total' => Application::count(),
            'pending' => Application::where('status', 'pending')->count(),
            'approved' => Application::where('status', 'approved')->count(),
            'rejected' => Application::where('status', 'rejected')->count(),
        ];
        
        return view('super-admin.applications.index', compact('applications', 'stats'));
    }
    
    public function show(Application $application)
    {
        $application->load(['user', 'reviewer']);
        return view('super-admin.applications.show', compact('application'));
    }
    

    public function approve(Application $application)
{
    $application->update([
        'status' => 'approved',
        'reviewed_by' => auth()->id(),
        'reviewed_at' => now(),
    ]);
    
    // Update user role using Spatie
    $user = $application->user;
    $type = $application->type;
    
    // Assign the appropriate role
    if ($type === 'author') {
        $user->assignRole('author');
        $user->author_approved_at = now();
        $user->author_approved_by = auth()->id();
    } elseif ($type === 'bookseller') {
        $user->assignRole('bookseller');
        $user->bookseller_approved_at = now();
        $user->bookseller_approved_by = auth()->id();
    } elseif ($type === 'publisher') {
        $user->assignRole('publisher');
        $user->publisher_approved_at = now();
        $user->publisher_approved_by = auth()->id();
    } elseif ($type === 'researcher') {
        $user->assignRole('researcher');
        $user->researcher_approved_at = now();
        $user->researcher_approved_by = auth()->id();
    }
    
    $user->save();
    
    return redirect()->back()->with('success', 'Application approved! User is now a ' . ucfirst($type) . '.');
}
    
    public function reject(Request $request, Application $application)
    {
        $request->validate([
            'admin_notes' => 'required|string|min:10',
        ]);
        
        $application->update([
            'status' => 'rejected',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'admin_notes' => $request->admin_notes,
        ]);
        
        return redirect()->back()->with('success', 'Application rejected.');
    }
    
    public function download(Application $application, $document)
    {
        $allowedDocuments = ['id_document', 'certificate_document', 'business_license', 'tax_certificate'];
        
        if (!in_array($document, $allowedDocuments)) {
            abort(404);
        }
        
        $path = $application->$document;
        
        if (!$path || !Storage::disk('public')->exists($path)) {
            abort(404);
        }
        
        return Storage::disk('public')->download($path);
    }
    
    public function destroy(Application $application)
    {
        // Delete associated documents
        $documents = ['id_document', 'certificate_document', 'business_license', 'tax_certificate'];
        foreach ($documents as $doc) {
            if ($application->$doc && Storage::disk('public')->exists($application->$doc)) {
                Storage::disk('public')->delete($application->$doc);
            }
        }
        
        $application->delete();
        
        return redirect()->route('super-admin.applications.index')->with('success', 'Application deleted successfully!');
    }
}