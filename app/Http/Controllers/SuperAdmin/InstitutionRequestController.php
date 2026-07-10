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
    $query = InstitutionCreationRequest::with(['user'])->whereHas('user');

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
        'total' => InstitutionCreationRequest::whereHas('user')->count(),
        'pending' => InstitutionCreationRequest::whereHas('user')->where('status', 'pending')->count(),
        'approved' => InstitutionCreationRequest::whereHas('user')->where('status', 'approved')->count(),
        'rejected' => InstitutionCreationRequest::whereHas('user')->where('status', 'rejected')->count(),
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
        return redirect()->back()->with('error', 'This request has already been processed.');
    }

    if (!$creationRequest->user) {
        return redirect()->back()->with('error', 'User not found for this request.');
    }

    if ($creationRequest->user->institution_id) {
        return redirect()->back()->with('error', 'User is already a member of an institution.');
    }

    // ==========================================
    // ✅ FIX: USE USER'S EMAIL IF MISSING
    // ==========================================
    if (empty($creationRequest->email)) {
        // Use the user's email as fallback
        $creationRequest->email = $creationRequest->user->email;
        $creationRequest->save();
    }

    // Create the institution
    $institution = Institution::create([
        'name' => $creationRequest->name,
        'slug' => Str::slug($creationRequest->name) . '-' . uniqid(),
        'type' => $creationRequest->type ?? 'library',
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

    // ==========================================
    // ASSIGN ROLE BASED ON INSTITUTION TYPE
    // ==========================================
    $user = $creationRequest->user;
    $role = User::getRoleForInstitutionType($creationRequest->type ?? 'library');

    // Update user
    $user->update([
        'institution_id' => $institution->id,
        'is_institution_admin' => true,
        'role' => $role,
    ]);

    // Assign Spatie role
    $user->assignRole($role);

    // Update the request
    $creationRequest->update([
        'status' => 'approved',
        'approved_by' => auth()->id(),
        'approved_at' => now(),
    ]);

    return redirect()->route('super-admin.institution-requests.index')
        ->with('success', "Institution '{$institution->name}' has been created successfully! User is now a {$role}.");
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