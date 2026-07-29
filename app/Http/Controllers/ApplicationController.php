<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ApplicationController extends Controller
{
  public function create($type)
{
    // Map 'creator' to 'author' for backward compatibility
    if ($type === 'creator') {
        $type = 'author';
    }
    
    // ✅ MATCH store() validation exactly
    $validTypes = ['author', 'bookseller', 'publisher', 'researcher'];
    if (!in_array($type, $validTypes)) {
        abort(404, 'Invalid application type.');
    }
    
    return view('applications.create', compact('type'));
}
   /**
     * Store a newly created application.
     */
    public function store(Request $request)
    {
        // ==========================================
        // 🔥 DEBUGGING - LOG EVERYTHING
        // ==========================================
        Log::emergency('🚀🚀🚀 STORE METHOD STARTED 🚀🚀🚀');
        Log::emergency('Request Method: ' . $request->method());
        Log::emergency('Request URL: ' . $request->fullUrl());
        Log::emergency('Request IP: ' . $request->ip());
        Log::emergency('User Agent: ' . $request->userAgent());
        
        // Check authentication
        if (!auth()->check()) {
            Log::emergency('❌ USER NOT AUTHENTICATED!');
            return redirect()->route('login')
                ->with('error', 'Please login to submit an application.');
        }
        
        Log::emergency('✅ User Authenticated: ' . auth()->id() . ' - ' . auth()->user()->email);
        Log::emergency('All Request Data:', $request->all());
        Log::emergency('Files:', $request->allFiles() ? array_keys($request->allFiles()) : 'No files');
        
        // ==========================================
        // VALIDATION
        // ==========================================
        try {
            $validated = $request->validate([
                'type' => 'required|in:author,bookseller,publisher,researcher',
                'full_name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'country' => 'required|string|max:100',
                'country_code' => 'nullable|string|max:10',
                'phone' => 'required|string|max:20|regex:/^[\+\d\s\-\(\)]+$/',
                'biography' => 'required|string|min:10|max:5000',
                'passport_photo' => 'required|file|mimes:jpg,jpeg,png|max:5120',
                'supporting_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
            ]);
            
            Log::emergency('✅ Validation Passed!');
            Log::emergency('Validated Data:', $validated);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::emergency('❌ Validation Failed!');
            Log::emergency('Validation Errors:', $e->errors());
            
            return redirect()->back()
                ->withInput()
                ->withErrors($e->errors());
        } catch (\Exception $e) {
            Log::emergency('❌ Unexpected Validation Error: ' . $e->getMessage());
            Log::emergency($e->getTraceAsString());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Validation error: ' . $e->getMessage());
        }
        
        // ==========================================
        // PROCESS FILES
        // ==========================================
        try {
            Log::emergency('📁 Processing Files...');
            
            // Upload Passport Photo
            if ($request->hasFile('passport_photo')) {
                $passportPhoto = $request->file('passport_photo')->store('applications/passports', 'public');
                Log::emergency('✅ Passport Photo Uploaded: ' . $passportPhoto);
            } else {
                Log::emergency('❌ No passport_photo file found!');
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Passport photo is required.');
            }
            
            // Upload Supporting Document - Optional
            $supportingDocument = null;
            if ($request->hasFile('supporting_document')) {
                $supportingDocument = $request->file('supporting_document')->store('applications/documents', 'public');
                Log::emergency('✅ Supporting Document Uploaded: ' . $supportingDocument);
            } else {
                Log::emergency('ℹ️ No supporting document uploaded (optional)');
            }
            
        } catch (\Exception $e) {
            Log::emergency('❌ File Upload Error: ' . $e->getMessage());
            Log::emergency($e->getTraceAsString());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'File upload error: ' . $e->getMessage());
        }
        
        // ==========================================
        // PROCESS PHONE NUMBER
        // ==========================================
        try {
            // Combine country code and phone if needed
            $fullPhone = $request->phone;
            if ($request->country_code && !str_starts_with($request->phone, $request->country_code)) {
                $fullPhone = $request->country_code . ltrim($request->phone, '+');
                Log::emergency('📞 Phone combined: ' . $fullPhone);
            } else {
                Log::emergency('📞 Phone: ' . $fullPhone);
            }
            
        } catch (\Exception $e) {
            Log::emergency('❌ Phone Processing Error: ' . $e->getMessage());
            $fullPhone = $request->phone; // Fallback
        }
        
        // ==========================================
        // CREATE APPLICATION
        // ==========================================
        try {
            Log::emergency('📝 Creating Application Record...');
            
            $application = Application::create([
                'user_id' => auth()->id(),
                'type' => $request->type,
                'full_name' => $request->full_name,
                'email' => $request->email,
                'country' => $request->country,
                'country_code' => $request->country_code,
                'phone' => $fullPhone,
                'biography' => $request->biography,
                'message' => $request->biography, // For backward compatibility
                'passport_photo' => $passportPhoto,
                'supporting_document' => $supportingDocument,
                'status' => 'pending',
            ]);
            
            Log::emergency('✅ Application Created! ID: ' . $application->id);
            Log::emergency('Application Data:', $application->toArray());
            
            return redirect()->route('dashboard')
                ->with('success', '✅ Your application has been submitted for review! We will contact you if we need more information.');
                
        } catch (\Exception $e) {
            Log::emergency('❌ Database Error: ' . $e->getMessage());
            Log::emergency($e->getTraceAsString());
            
            // Delete uploaded files if something goes wrong
            try {
                if (isset($passportPhoto) && Storage::disk('public')->exists($passportPhoto)) {
                    Storage::disk('public')->delete($passportPhoto);
                    Log::emergency('🗑️ Deleted passport photo: ' . $passportPhoto);
                }
                if (isset($supportingDocument) && Storage::disk('public')->exists($supportingDocument)) {
                    Storage::disk('public')->delete($supportingDocument);
                    Log::emergency('🗑️ Deleted supporting document: ' . $supportingDocument);
                }
            } catch (\Exception $deleteError) {
                Log::emergency('❌ Error deleting files: ' . $deleteError->getMessage());
            }
            
            return redirect()->back()
                ->withInput()
                ->with('error', '❌ Failed to submit application. Error: ' . $e->getMessage());
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
    
   public function approve(Application $application)
{
    $application->update([
        'status' => 'approved',
        'reviewed_by' => auth()->id(),
        'reviewed_at' => now(),
    ]);

    $user = $application->user;
    $user->role = $application->type;
    $user->save();

    // Sync the Spatie role so role-based queries/checks pick this user up
    $user->syncRoles([$application->type]);

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