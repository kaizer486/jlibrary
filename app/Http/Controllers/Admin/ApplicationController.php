<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function index(Request $request)
    {
        $query = Application::with(['user']); // Remove 'approvedBy' if it doesn't exist
        
        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        // Filter by status
        if ($request->filled('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }
        
        // Filter by type
        if ($request->filled('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }
        
        $applications = $query->orderBy('created_at', 'desc')->paginate(20);
        
        // Stats
        $stats = [
            'total' => Application::count(),
            'pending' => Application::where('status', 'pending')->count(),
            'approved' => Application::where('status', 'approved')->count(),
            'rejected' => Application::where('status', 'rejected')->count(),
        ];
        
        return view('admin.applications.index', compact('applications', 'stats'));
    }

    public function show(Application $application)
    {
        // Load relationships - only load those that exist
        $application->load(['user']);
        
        // Only load reviewer if the relationship exists
        if (method_exists($application, 'reviewer')) {
            $application->load(['reviewer']);
        }
        
        return view('admin.applications.show', compact('application'));
    }

    public function approve(Application $application)
    {
        $application->update([
            'status' => 'approved',
            'reviewed_at' => now(),
            'reviewed_by' => auth()->id(),
        ]);
        
        // Update user role based on application type
        $user = $application->user;
        if ($user) {
            switch ($application->type) {
                case 'author':
                    $user->role = 'author';
                    break;
                case 'bookseller':
                    $user->role = 'bookseller';
                    break;
                case 'publisher':
                    $user->role = 'publisher';
                    break;
                case 'researcher':
                    $user->role = 'researcher';
                    break;
            }
            $user->save();
        }
        
        return redirect()->route('admin.applications.index')
            ->with('success', 'Application approved successfully!');
    }

    public function reject(Request $request, Application $application)
    {
        $request->validate([
            'admin_notes' => 'required|string|min:5',
        ]);
        
        $application->update([
            'status' => 'rejected',
            'admin_notes' => $request->admin_notes,
            'reviewed_at' => now(),
            'reviewed_by' => auth()->id(),
        ]);
        
        return redirect()->route('admin.applications.index')
            ->with('success', 'Application rejected successfully!');
    }

    public function download(Application $application, $document)
    {
        // Check if the document exists
        $documentPath = $application->$document;
        
        if (!$documentPath) {
            abort(404, 'Document not found');
        }
        
        // Check if file exists in storage
        if (!\Storage::disk('public')->exists($documentPath)) {
            abort(404, 'File not found');
        }
        
        return response()->download(
            \Storage::disk('public')->path($documentPath),
            $document . '_' . $application->user->full_name . '.pdf'
        );
    }

    public function destroy(Application $application)
    {
        // Delete associated files
        $documents = ['id_document', 'certificate_document', 'business_license', 'tax_certificate'];
        foreach ($documents as $doc) {
            if ($application->$doc && \Storage::disk('public')->exists($application->$doc)) {
                \Storage::disk('public')->delete($application->$doc);
            }
        }
        
        $application->delete();
        
        return redirect()->route('admin.applications.index')
            ->with('success', 'Application deleted successfully!');
    }
}