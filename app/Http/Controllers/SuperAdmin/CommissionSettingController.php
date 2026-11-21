<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;

class CommissionSettingController extends Controller
{
    public function index()
    {
        $percentage = SiteSetting::where('key', 'platform_commission_percentage')->first()->value ?? 10;
        return view('super-admin.commissions.settings', compact('percentage'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'percentage' => 'required|numeric|min:0|max:100'
        ]);

        SiteSetting::updateOrCreate(
            ['key' => 'platform_commission_percentage'],
            ['value' => $request->percentage, 'group' => 'commissions']
        );

        return back()->with('success', 'Platform commission percentage updated to ' . $request->percentage . '%');
    }
}