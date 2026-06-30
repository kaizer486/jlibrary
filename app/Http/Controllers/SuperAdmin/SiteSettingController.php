<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;

class SiteSettingController extends Controller
{
    /**
     * Display the site settings page.
     */
    public function index()
    {
        $settings = SiteSetting::all()->pluck('value', 'key')->toArray();
        
        return view('super-admin.site-settings.index', compact('settings'));
    }

    /**
     * Update site settings.
     */
    public function update(Request $request)
    {
        $request->validate([
            'vision' => 'nullable|string',
            'mission' => 'nullable|string',
            'motto' => 'nullable|string|max:255',
            'platform_message' => 'nullable|string',
            'contact_email' => 'nullable|email',
            'support_email' => 'nullable|email',
            'contact_phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'announcement_1' => 'nullable|string',
            'announcement_2' => 'nullable|string',
            'announcement_3' => 'nullable|string',
        ]);

        // Update each setting
        foreach ($request->except(['_token', '_method']) as $key => $value) {
            SiteSetting::setValue($key, $value, 'content');
        }

        return redirect()->route('super-admin.site-settings.index')
            ->with('success', 'Site settings updated successfully!');
    }
}