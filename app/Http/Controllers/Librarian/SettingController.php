<?php

namespace App\Http\Controllers\Librarian;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Display librarian settings page.
     */
    public function index()
    {
        $institution = auth()->user()->institution;
        
        if (!$institution) {
            abort(403, 'You do not belong to any institution.');
        }

        return view('librarian.settings.index', compact('institution'));
    }

    /**
     * Update librarian settings.
     */
    public function update(Request $request)
    {
        $user = auth()->user();
        
        $validated = $request->validate([
            'email_notifications' => 'nullable|boolean',
            'approval_alerts' => 'nullable|boolean',
            'member_reports' => 'nullable|boolean',
            'new_member_alerts' => 'nullable|boolean',
            'default_view' => 'nullable|string|in:grid,list',
            'per_page' => 'nullable|integer|in:15,25,50,100',
        ]);

        // Store settings in session or user meta
        // For now, store in session as a simple example
        session(['librarian_settings' => $validated]);

        return redirect()->route('librarian.settings')
            ->with('success', 'Settings updated successfully!');
    }
}