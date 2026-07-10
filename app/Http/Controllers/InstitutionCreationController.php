<?php

namespace App\Http\Controllers;

use App\Models\InstitutionCreationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InstitutionCreationController extends Controller
{
    /**
     * Show the request creation form.
     */
    public function create()
    {
        // Check if user already has a pending request
        $pendingRequest = InstitutionCreationRequest::where('user_id', auth()->id())
            ->where('status', 'pending')
            ->first();

        if ($pendingRequest) {
            return redirect()->route('institution.my-requests')
                ->with('info', 'You already have a pending institution creation request.');
        }

        return view('institution.requests.create');
    }

    /**
     * Store a new institution creation request.
     */
    public function store(Request $request)
    {
        // Check if user already has a pending request
        $pendingRequest = InstitutionCreationRequest::where('user_id', auth()->id())
            ->where('status', 'pending')
            ->first();

        if ($pendingRequest) {
            return redirect()->route('institution.my-requests')
                ->with('error', 'You already have a pending request.');
        }

        // ==========================================
        // ALL FIELDS REQUIRED - UPDATED VALIDATION
        // ==========================================
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:school,college,university,library,bookstore,publisher,research_center,academy,institute,other',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:50',
            'city' => 'required|string|max:255',
            'region' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'description' => 'required|string|min:20|max:1000',
            'website' => 'required|url|max:255',
            'motivation' => 'required|string|min:20|max:1000',
            'document' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240', // 10MB max
        ], [
            // Custom error messages
            'name.required' => 'Please enter the institution name.',
            'type.required' => 'Please select the institution type.',
            'type.in' => 'Please select a valid institution type.',
            'email.required' => 'Please enter the contact email.',
            'email.email' => 'Please enter a valid email address.',
            'phone.required' => 'Please enter the phone number.',
            'city.required' => 'Please enter the city.',
            'region.required' => 'Please enter the region or state.',
            'address.required' => 'Please enter the address.',
            'description.required' => 'Please describe your institution.',
            'description.min' => 'Description must be at least 20 characters.',
            'website.required' => 'Please enter the website URL.',
            'website.url' => 'Please enter a valid website URL (e.g., https://example.com).',
            'motivation.required' => 'Please explain your motivation.',
            'motivation.min' => 'Motivation must be at least 20 characters.',
            'document.required' => 'Please upload a supporting document.',
            'document.mimes' => 'Document must be PDF, Word, or image file.',
            'document.max' => 'Document size must not exceed 10MB.',
        ]);

        // Handle document upload
        $documentPath = null;
        if ($request->hasFile('document')) {
            $documentPath = $request->file('document')->store('institution-requests/documents', 'public');
        }

        $creationRequest = InstitutionCreationRequest::create([
            'user_id' => auth()->id(),
            'name' => $validated['name'],
            'type' => $validated['type'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'city' => $validated['city'],
            'region' => $validated['region'],
            'address' => $validated['address'],
            'description' => $validated['description'],
            'website' => $validated['website'],
            'motivation' => $validated['motivation'],
            'document_path' => $documentPath,
            'status' => 'pending',
        ]);

        return redirect()->route('institution.my-requests')
            ->with('success', 'Your institution creation request has been submitted successfully! Please wait for approval.');
    }

    /**
     * Display user's requests.
     */
    public function myRequests()
    {
        $requests = InstitutionCreationRequest::where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('institution.requests.my-requests', compact('requests'));
    }

    /**
     * Display a specific request.
     */
    public function show($id)
    {
        $request = InstitutionCreationRequest::where('user_id', auth()->id())
            ->findOrFail($id);

        return view('institution.requests.show', compact('request'));
    }

    /**
     * Cancel a pending request.
     */
    public function cancel($id)
    {
        $request = InstitutionCreationRequest::where('user_id', auth()->id())
            ->where('status', 'pending')
            ->findOrFail($id);

        // Delete document if exists
        if ($request->document_path) {
            Storage::disk('public')->delete($request->document_path);
        }

        $request->delete();

        return redirect()->route('institution.my-requests')
            ->with('success', 'Your request has been cancelled.');
    }
}