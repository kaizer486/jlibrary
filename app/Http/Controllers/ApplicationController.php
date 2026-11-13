<?php

namespace App\Http\Controllers;

use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

        $validTypes = ['author', 'bookseller', 'publisher', 'researcher'];
        if (!in_array($type, $validTypes, true)) {
            abort(404, 'Invalid application type.');
        }

        return view('applications.create', compact('type'));
    }

    public function store(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('login')
                ->with('error', 'Please login to submit an application.');
        }

        try {
            $validated = $request->validate([
                'type'                => 'required|in:author,bookseller,publisher,researcher',
                'full_name'           => 'required|string|max:255',
                'email'               => 'required|email|max:255',
                'country'             => 'required|string|max:100',
                'country_code'        => 'nullable|string|max:10',
                'phone'               => 'required|string|max:20|regex:/^[\+\d\s\-\(\)]+$/',
                'biography'           => 'required|string|min:10|max:5000',
                'passport_photo'      => 'required|file|mimes:jpg,jpeg,png|max:5120',
                'supporting_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withInput()->withErrors($e->errors());
        }

        // ---- Guard: block duplicate pending/approved application of same type ----
        $alreadyApplied = Application::where('user_id', auth()->id())
            ->where('type', $validated['type'])
            ->whereIn('status', ['pending', 'approved'])
            ->exists();

        if ($alreadyApplied) {
            return redirect()->route('dashboard')
                ->with('error', 'You already have an active ' . $validated['type']
                    . ' application. Please wait for it to be reviewed, or contact support.');
        }
        // --------------------------------------------------------------------------

        // Upload files (moved inside the transaction so we can clean up on failure)
        $passportPhoto = null;
        $supportingDocument = null;

        try {
            if (!$request->hasFile('passport_photo')) {
                return redirect()->back()->withInput()
                    ->with('error', 'Passport photo is required.');
            }
            $passportPhoto = $request->file('passport_photo')
                ->store('applications/passports', 'public');

            if ($request->hasFile('supporting_document')) {
                $supportingDocument = $request->file('supporting_document')
                    ->store('applications/documents', 'public');
            }
        } catch (\Exception $e) {
            Log::error('Application file upload error: ' . $e->getMessage());
            return redirect()->back()->withInput()
                ->with('error', 'File upload error. Please try again.');
        }

        $fullPhone = $request->phone;
        if ($request->country_code && !str_starts_with($request->phone, $request->country_code)) {
            $fullPhone = $request->country_code . ltrim($request->phone, '+');
        }

        try {
            DB::transaction(function () use ($validated, $passportPhoto, $supportingDocument, $fullPhone) {
                // Re-check inside the transaction with a row lock, to close the
                // simultaneous-submit race that the earlier exists() check can't.
                $locked = Application::where('user_id', auth()->id())
                    ->where('type', $validated['type'])
                    ->whereIn('status', ['pending', 'approved'])
                    ->lockForUpdate()
                    ->exists();

                if ($locked) {
                    throw new \RuntimeException('DUPLICATE_APPLICATION');
                }

                Application::create([
                    'user_id'             => auth()->id(),
                    'type'                => $validated['type'],
                    'full_name'           => $validated['full_name'],
                    'email'               => $validated['email'],
                    'country'             => $validated['country'],
                    'country_code'        => $validated['country_code'] ?? null,
                    'phone'               => $fullPhone,
                    'biography'           => $validated['biography'],
                    'message'             => $validated['biography'],
                    'passport_photo'      => $passportPhoto,
                    'supporting_document' => $supportingDocument,
                    'status'              => 'pending',
                ]);
            });

            return redirect()->route('dashboard')
                ->with('success', '✅ Your application has been submitted for review! We will contact you if we need more information.');

        } catch (\RuntimeException $e) {
            // Duplicate detected inside the transaction — clean up files
            $this->deleteFilesIfExist([$passportPhoto, $supportingDocument]);

            if ($e->getMessage() === 'DUPLICATE_APPLICATION') {
                return redirect()->route('dashboard')
                    ->with('error', 'You already have an active ' . $validated['type']
                        . ' application. Please wait for it to be reviewed.');
            }

            Log::error('Application creation error: ' . $e->getMessage());
            return redirect()->back()->withInput()
                ->with('error', 'Failed to submit application. Please try again or contact support.');

        } catch (\Exception $e) {
            Log::error('Application creation error: ' . $e->getMessage());
            $this->deleteFilesIfExist([$passportPhoto, $supportingDocument]);

            return redirect()->back()->withInput()
                ->with('error', 'Failed to submit application. Please try again or contact support.');
        }
    }

    // -------------------- Admin views --------------------

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
            'total'    => Application::count(),
            'pending'  => Application::where('status', 'pending')->count(),
            'approved' => Application::where('status', 'approved')->count(),
            'rejected' => Application::where('status', 'rejected')->count(),
        ];

        return view('admin.applications.index', compact('applications', 'stats'));
    }

    public function show(Application $application)
    {
        return view('admin.applications.show', compact('application'));
    }

    // -------------------- Approve --------------------

    public function approve(Application $application)
    {
        // Idempotency: don't re-approve
        if ($application->status !== 'pending') {
            return back()->with('info', 'This application has already been ' . $application->status . '.');
        }

        $type = $application->type;

        if (!in_array($type, ['author', 'bookseller', 'publisher', 'researcher'], true)) {
            return back()->with('error', 'Unknown application type: ' . $type);
        }

        DB::transaction(function () use ($application, $type) {
            $application->update([
                'status'      => 'approved',
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
            ]);

            $user = $application->user;
            if (!$user) {
                return;
            }

            // Legacy role column
            $user->role = $type;

            // Per-type audit fields — not in $fillable, so forceFill
            $user->forceFill([
                $type . '_approved_at' => now(),
                $type . '_approved_by' => auth()->id(),
            ]);

            // Backfill phone if missing
            if (empty($user->phone) && $application->phone) {
                $user->phone = $application->phone;
            }

            $user->save();

            // Add the Spatie role without destroying any other roles the user
            // already has (assignRole is idempotent — no duplicate pivot rows).
            if (!$user->hasRole($type)) {
                $user->assignRole($type);
            }
        });

        return back()->with('success', ucfirst($type) . ' application approved! User role updated.');
    }

    // -------------------- Reject --------------------

    public function reject(Request $request, Application $application)
    {
        // Idempotency: don't reject an already-processed application
        if ($application->status !== 'pending') {
            return back()->with('info', 'This application has already been ' . $application->status . '.');
        }

        $request->validate([
            'admin_notes' => 'required|string|min:10',
        ]);

        $application->update([
            'status'      => 'rejected',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'admin_notes' => $request->admin_notes,
        ]);

        return back()->with('success', 'Application rejected.');
    }

    // -------------------- Documents --------------------

    public function download(Application $application, $document)
    {
        $allowedDocuments = ['passport_photo', 'supporting_document', 'business_license', 'tax_certificate'];

        if (!in_array($document, $allowedDocuments, true)) {
            abort(404);
        }

        $path = $application->$document;

        if (!$path || !Storage::disk('public')->exists($path)) {
            abort(404);
        }

        return Storage::disk('public')->download($path);
    }

    // -------------------- Helpers --------------------

    private function deleteFilesIfExist(array $paths): void
    {
        foreach ($paths as $path) {
            if ($path && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }
    }
}