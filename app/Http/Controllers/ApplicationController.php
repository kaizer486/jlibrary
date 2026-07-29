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
    if (!auth()->check()) {
        return redirect()->route('login')
            ->with('error', 'Please login to submit an application.');
    }

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
    } catch (\Illuminate\Validation\ValidationException $e) {
        return redirect()->back()
            ->withInput()
            ->withErrors($e->errors());
    }

    try {
        if ($request->hasFile('passport_photo')) {
            $passportPhoto = $request->file('passport_photo')->store('applications/passports', 'public');
        } else {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Passport photo is required.');
        }

        $supportingDocument = null;
        if ($request->hasFile('supporting_document')) {
            $supportingDocument = $request->file('supporting_document')->store('applications/documents', 'public');
        }
    } catch (\Exception $e) {
        Log::error('Application file upload error: ' . $e->getMessage());

        return redirect()->back()
            ->withInput()
            ->with('error', 'File upload error. Please try again.');
    }

    $fullPhone = $request->phone;
    if ($request->country_code && !str_starts_with($request->phone, $request->country_code)) {
        $fullPhone = $request->country_code . ltrim($request->phone, '+');
    }

    try {
        $application = Application::create([
            'user_id' => auth()->id(),
            'type' => $request->type,
            'full_name' => $request->full_name,
            'email' => $request->email,
            'country' => $request->country,
            'country_code' => $request->country_code,
            'phone' => $fullPhone,
            'biography' => $request->biography,
            'message' => $request->biography,
            'passport_photo' => $passportPhoto,
            'supporting_document' => $supportingDocument,
            'status' => 'pending',
        ]);

        return redirect()->route('dashboard')
            ->with('success', '✅ Your application has been submitted for review! We will contact you if we need more information.');

    } catch (\Exception $e) {
        Log::error('Application creation error: ' . $e->getMessage());

        if (isset($passportPhoto) && Storage::disk('public')->exists($passportPhoto)) {
            Storage::disk('public')->delete($passportPhoto);
        }
        if (isset($supportingDocument) && Storage::disk('public')->exists($supportingDocument)) {
            Storage::disk('public')->delete($supportingDocument);
        }

        return redirect()->back()
            ->withInput()
            ->with('error', 'Failed to submit application. Please try again or contact support.');
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