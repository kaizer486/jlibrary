<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ApplicationController extends Controller
{
    // Show application form
    public function create($type)
    {
        $validTypes = ['author', 'bookseller', 'publisher', 'researcher'];
        
        if (!in_array($type, $validTypes)) {
            abort(404);
        }
        
        // Check if already has pending application
        if (auth()->user()->hasPendingApplication($type)) {
            return redirect()->route('dashboard')->with('error', 'You already have a pending application for ' . ucfirst($type));
        }
        
        // Check if already approved
        $method = 'isApproved' . ucfirst($type);
        if (method_exists(auth()->user(), $method) && auth()->user()->$method()) {
            return redirect()->route('dashboard')->with('error', 'You are already an approved ' . ucfirst($type));
        }
        
        return view('applications.create', compact('type'));
    }
    
    // Store application
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:author,bookseller,publisher,researcher',
            'message' => 'nullable|string',
            'business_name' => 'nullable|string|max:255',
            'business_address' => 'nullable|string',
            'tax_id' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'id_document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'certificate_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'business_license' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'tax_certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);
        
        // Upload documents
        $idDocument = $request->file('id_document')->store('applications/documents', 'public');
        $certificateDocument = $request->hasFile('certificate_document') ? $request->file('certificate_document')->store('applications/documents', 'public') : null;
        $businessLicense = $request->hasFile('business_license') ? $request->file('business_license')->store('applications/documents', 'public') : null;
        $taxCertificate = $request->hasFile('tax_certificate') ? $request->file('tax_certificate')->store('applications/documents', 'public') : null;
        
        Application::create([
            'user_id' => auth()->id(),
            'type' => $request->type,
            'message' => $request->message,
            'business_name' => $request->business_name,
            'business_address' => $request->business_address,
            'tax_id' => $request->tax_id,
            'phone' => $request->phone,
            'id_document' => $idDocument,
            'certificate_document' => $certificateDocument,
            'business_license' => $businessLicense,
            'tax_certificate' => $taxCertificate,
            'status' => 'pending',
        ]);
        
        return redirect()->route('dashboard')->with('success', 'Your application has been submitted for review!');
    }
    
    // Admin: View all applications
    public function index(Request $request)
    {
        $query = Application::with(['user', 'reviewer']);
        
        if ($request->type && $request->type !== 'all') {
            $query->where('type', $request->type);
        }
        
        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        
        $applications = $query->latest()->paginate(20);
        
        $stats = [
            'total' => Application::count(),
            'pending' => Application::where('status', 'pending')->count(),
            'approved' => Application::where('status', 'approved')->count(),
            'rejected' => Application::where('status', 'rejected')->count(),
        ];
        
        return view('admin.applications.index', compact('applications', 'stats'));
    }
    
    // Admin: Show application details
    public function show(Application $application)
    {
        return view('admin.applications.show', compact('application'));
    }
    
    // Admin: Approve application
    public function approve(Application $application)
    {
        $application->update([
            'status' => 'approved',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);
        
        // Update user role
        $user = $application->user;
        $user->role = $application->type;
        $user->save();
        
        return redirect()->back()->with('success', ucfirst($application->type) . ' application approved! User role updated.');
    }
    
    // Admin: Reject application
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
    
    // Download document
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
}