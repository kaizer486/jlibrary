<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Founder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FounderController extends Controller
{
    /**
     * Display a listing of founders.
     */
    public function index()
    {
        $founders = Founder::ordered()->get();
        return view('super-admin.founders.index', compact('founders'));
    }

    /**
     * Show the form for creating a new founder.
     *//**
 * Show the form for creating a new founder.
 */
public function create()
{
    // Get all founders ordered by their current order
    $founders = Founder::orderBy('order')->get();
    
    return view('super-admin.founders.create', compact('founders'));
}
    /**
     * Store a newly created founder.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'title' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'twitter' => 'nullable|string|max:255',
            'instagram' => 'nullable|string|max:255',
            'facebook' => 'nullable|string|max:255',
            'tiktok' => 'nullable|string|max:255',
            'whatsapp' => 'nullable|string|max:255',
            'youtube' => 'nullable|string|max:255',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean'
        ]);

        $data = $request->except(['twitter', 'instagram', 'facebook', 'tiktok', 'whatsapp', 'youtube']);

        // Handle social links
        $socialLinks = [];
        if ($request->twitter) $socialLinks['twitter'] = $request->twitter;
        if ($request->instagram) $socialLinks['instagram'] = $request->instagram;
        if ($request->facebook) $socialLinks['facebook'] = $request->facebook;
        if ($request->tiktok) $socialLinks['tiktok'] = $request->tiktok;
        if ($request->whatsapp) $socialLinks['whatsapp'] = $request->whatsapp;
        if ($request->youtube) $socialLinks['youtube'] = $request->youtube;
        
        $data['social_links'] = json_encode($socialLinks);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('founders', 'public');
            $data['photo'] = $photoPath;
        }

        $data['is_active'] = $request->has('is_active');
        $data['order'] = $request->order ?? 0;

        Founder::create($data);

        return redirect()->route('super-admin.founders.index')
            ->with('success', 'Founder created successfully!');
    }

    /**
     * Show the form for editing the specified founder.
     */
    public function edit(Founder $founder)
    {
        return view('super-admin.founders.edit', compact('founder'));
    }

    /**
     * Update the specified founder.
     */
    public function update(Request $request, Founder $founder)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'title' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'twitter' => 'nullable|string|max:255',
            'instagram' => 'nullable|string|max:255',
            'facebook' => 'nullable|string|max:255',
            'tiktok' => 'nullable|string|max:255',
            'whatsapp' => 'nullable|string|max:255',
            'youtube' => 'nullable|string|max:255',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean'
        ]);

        $data = $request->except(['twitter', 'instagram', 'facebook', 'tiktok', 'whatsapp', 'youtube']);

        // Handle social links
        $socialLinks = [];
        if ($request->twitter) $socialLinks['twitter'] = $request->twitter;
        if ($request->instagram) $socialLinks['instagram'] = $request->instagram;
        if ($request->facebook) $socialLinks['facebook'] = $request->facebook;
        if ($request->tiktok) $socialLinks['tiktok'] = $request->tiktok;
        if ($request->whatsapp) $socialLinks['whatsapp'] = $request->whatsapp;
        if ($request->youtube) $socialLinks['youtube'] = $request->youtube;
        
        $data['social_links'] = json_encode($socialLinks);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo
            if ($founder->photo && Storage::disk('public')->exists($founder->photo)) {
                Storage::disk('public')->delete($founder->photo);
            }
            
            $photoPath = $request->file('photo')->store('founders', 'public');
            $data['photo'] = $photoPath;
        }

        $data['is_active'] = $request->has('is_active');

        $founder->update($data);

        return redirect()->route('super-admin.founders.index')
            ->with('success', 'Founder updated successfully!');
    }

    /**
     * Remove the specified founder.
     */
    public function destroy(Founder $founder)
    {
        // Delete photo
        if ($founder->photo && Storage::disk('public')->exists($founder->photo)) {
            Storage::disk('public')->delete($founder->photo);
        }

        $founder->delete();

        return redirect()->route('super-admin.founders.index')
            ->with('success', 'Founder deleted successfully!');
    }

    /**
     * Toggle active status of founder.
     */
    public function toggleStatus(Founder $founder)
    {
        $founder->is_active = !$founder->is_active;
        $founder->save();

        return redirect()->route('super-admin.founders.index')
            ->with('success', 'Founder status updated!');
    }

    /**
     * Reorder founders.
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*' => 'exists:founders,id'
        ]);

        foreach ($request->items as $index => $itemId) {
            Founder::where('id', $itemId)->update(['order' => $index + 1]);
        }

        return response()->json(['success' => true]);
    }
}