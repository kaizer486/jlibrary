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

        // ✅ REMOVED: Institution check - users can request even if they're in an institution
        // if (auth()->user()->institution_id) {
        //     return redirect()->route('dashboard')
        //         ->with('info', 'You are already a member of an institution.');
        // }

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

        // ✅ REMOVED: Institution check
        // if (auth()->user()->institution_id) {
        //     return redirect()->route('dashboard')
        //         ->with('error', 'You are already a member of an institution.');
        // }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:school,college,university,library,bookstore,publisher,research_center,academy,institute',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'city' => 'nullable|string|max:255',
            'region' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
            'description' => 'nullable|string|max:1000',
            'website' => 'nullable|url|max:255',
            'motivation' => 'nullable|string|max:1000',
            'document' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
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
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'city' => $validated['city'] ?? null,
            'region' => $validated['region'] ?? null,
            'address' => $validated['address'] ?? null,
            'description' => $validated['description'] ?? null,
            'website' => $validated['website'] ?? null,
            'motivation' => $validated['motivation'] ?? null,
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