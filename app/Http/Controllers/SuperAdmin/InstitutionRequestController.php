<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Institution;
use App\Models\InstitutionCreationRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class InstitutionRequestController extends Controller
{
    /**
     * Display a listing of institution creation requests.
     */
    public function index(Request $request)
    {
        $query = InstitutionCreationRequest::with(['user']);

        // Filter by status
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Search by name or user email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function($q2) use ($search) {
                      $q2->where('email', 'LIKE', "%{$search}%")
                         ->orWhere('full_name', 'LIKE', "%{$search}%");
                  });
            });
        }

        $requests = $query->latest()->paginate(15)->appends($request->query());

        $stats = [
            'total' => InstitutionCreationRequest::count(),
            'pending' => InstitutionCreationRequest::where('status', 'pending')->count(),
            'approved' => InstitutionCreationRequest::where('status', 'approved')->count(),
            'rejected' => InstitutionCreationRequest::where('status', 'rejected')->count(),
        ];

        return view('super-admin.institution-requests.index', compact('requests', 'stats'));
    }

    /**
     * Display a specific institution creation request.
     */
    public function show($id)
    {
        $request = InstitutionCreationRequest::with(['user'])->findOrFail($id);

        return view('super-admin.institution-requests.show', compact('request'));
    }

    /**
     * Approve an institution creation request.
     */
    public function approve($id)
    {
        $creationRequest = InstitutionCreationRequest::with(['user'])->findOrFail($id);

        if ($creationRequest->status !== 'pending') {
            return redirect()->back()
                ->with('error', 'This request has already been processed.');
        }

        // Check if user is already in an institution
        if ($creationRequest->user->institution_id) {
            return redirect()->back()
                ->with('error', 'User is already a member of an institution.');
        }

        // Create the institution
        $institution = Institution::create([
            'name' => $creationRequest->name,
            'slug' => Str::slug($creationRequest->name) . '-' . uniqid(),
            'type' => $creationRequest->type,
            'email' => $creationRequest->email,
            'phone' => $creationRequest->phone,
            'city' => $creationRequest->city,
            'region' => $creationRequest->region,
            'address' => $creationRequest->address,
            'description' => $creationRequest->description,
            'website' => $creationRequest->website,
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        // Create institution wallet
        $institution->createWallet();

        // Assign user as institution admin
        $user = $creationRequest->user;
        $user->update([
            'institution_id' => $institution->id,
            'is_institution_admin' => true,
            'role' => 'institution_admin',
        ]);

        // Assign Spatie role
        $user->assignRole('institution_admin');

        // Update the request
        $creationRequest->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        // Fire event (you can implement this later)
        // event(new InstitutionCreated($institution, $user));

        return redirect()->route('super-admin.institution-requests.index')
            ->with('success', "Institution '{$institution->name}' has been created successfully!");
    }

    /**
     * Reject an institution creation request.
     */
    public function reject(Request $request, $id)
    {
        $creationRequest = InstitutionCreationRequest::findOrFail($id);

        if ($creationRequest->status !== 'pending') {
            return redirect()->back()
                ->with('error', 'This request has already been processed.');
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        // Delete document if exists
        if ($creationRequest->document_path) {
            Storage::disk('public')->delete($creationRequest->document_path);
        }

        $creationRequest->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return redirect()->route('super-admin.institution-requests.index')
            ->with('success', 'Institution creation request has been rejected.');
    }

    /**
     * Download supporting document.
     */
    public function download($id)
    {
        $creationRequest = InstitutionCreationRequest::findOrFail($id);

        if (!$creationRequest->document_path) {
            abort(404, 'No document found.');
        }

        $path = storage_path('app/public/' . $creationRequest->document_path);

        if (!file_exists($path)) {
            abort(404, 'Document not found.');
        }

        return response()->download($path);
    }
}