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
        'full_name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'country' => 'required|string|max:100',
        'country_code' => 'nullable|string|max:10',
        'phone' => 'required|string|max:20|regex:/^[\+\d\s\-\(\)]+$/',
        'biography' => 'required|string|min:50|max:5000',
        'passport_photo' => 'required|file|mimes:jpg,jpeg,png|max:5120',
        'supporting_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
    ]);
    
    try {
        // Upload Passport Photo
        $passportPhoto = $request->file('passport_photo')->store('applications/passports', 'public');
        
        // Upload Supporting Document - Optional
        $supportingDocument = $request->hasFile('supporting_document') 
            ? $request->file('supporting_document')->store('applications/documents', 'public') 
            : null;
        
        // Combine country code and phone if needed
        $fullPhone = $request->phone;
        if ($request->country_code && !str_starts_with($request->phone, $request->country_code)) {
            $fullPhone = $request->country_code . ltrim($request->phone, '+');
        }
        
        // Create application
        $application = Application::create([
            'user_id' => auth()->id(),
            'type' => $request->type,
            'full_name' => $request->full_name,
            'email' => $request->email,
            'country' => $request->country,
            'country_code' => $request->country_code,
            'phone' => $fullPhone,
            'message' => $request->biography,
            'biography' => $request->biography,
            'passport_photo' => $passportPhoto,
            'supporting_document' => $supportingDocument,
            'status' => 'pending',
        ]);
        
        return redirect()->route('dashboard')
            ->with('success', 'Your application has been submitted for review! We will contact you at ' . $fullPhone . ' if we need more information.');
            
    } catch (\Exception $e) {
        // Delete uploaded files if something goes wrong
        if (isset($passportPhoto) && Storage::disk('public')->exists($passportPhoto)) {
            Storage::disk('public')->delete($passportPhoto);
        }
        
        return redirect()->back()
            ->withInput()
            ->with('error', 'Failed to submit application. Please try again. Error: ' . $e->getMessage());
    }
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
        
        // Optional: Update user's phone number if not set
        if (empty($user->phone)) {
            $user->phone = $application->phone;
            $user->save();
        }
        
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
        $allowedDocuments = ['passport_photo', 'supporting_document', 'business_license', 'tax_certificate'];
        
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