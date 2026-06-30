<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\User;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function index(Request $request)
    {
        $query = Application::with(['user', 'approvedBy']);

        // Search by user name or email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
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
        $application->load(['user', 'approvedBy']);
        return view('admin.applications.show', compact('application'));
    }

    public function approve(Application $application)
    {
        $user = $application->user;
        $type = $application->type;

        // Assign the appropriate role using Spatie
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

        // Update application status
        $application->status = 'approved';
        $application->approved_at = now();
        $application->approved_by = auth()->id();
        $application->save();

        return redirect()->route('admin.applications.index')
            ->with('success', ucfirst($type) . ' application approved successfully!');
    }

    public function reject(Request $request, Application $application)
    {
        $request->validate([
            'admin_notes' => 'required|string|min:10',
        ]);

        $application->status = 'rejected';
        $application->approved_by = auth()->id();
        $application->admin_notes = $request->admin_notes;
        $application->save();

        return redirect()->route('admin.applications.index')
            ->with('error', 'Application rejected.');
    }

    public function destroy(Application $application)
    {
        // Delete associated documents if they exist
        $documents = ['id_document', 'certificate_document', 'business_license', 'tax_certificate'];
        foreach ($documents as $doc) {
            if ($application->$doc && Storage::disk('public')->exists($application->$doc)) {
                Storage::disk('public')->delete($application->$doc);
            }
        }

        $application->delete();

        return redirect()->route('admin.applications.index')
            ->with('success', 'Application deleted successfully!');
    }
}