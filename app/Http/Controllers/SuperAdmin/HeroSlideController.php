<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\HeroSlide;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HeroSlideController extends Controller
{
    /**
     * Display a listing of hero slides.
     */
    public function index()
    {
        $slides = HeroSlide::ordered()->get();
        return view('super-admin.hero-slides.index', compact('slides'));
    }

public function create()
{
    // Get the count of existing slides for the order field
    $slideCount = HeroSlide::count();
    
    $slideTypes = [
        'dashboard' => '📊 Dashboard Showcase',
        'books' => '📚 Books/Library Showcase',
        'ai' => '🤖 AI Assistant Showcase',
        'community' => '🌍 Community Showcase',
        'custom' => '🎨 Custom Layout'
    ];
    
    return view('super-admin.hero-slides.create', compact('slideTypes', 'slideCount'));
}

    /**
     * Store a newly created hero slide.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'slide_type' => 'required|string|in:dashboard,books,ai,community,custom',
            'badge_text' => 'nullable|string|max:100',
            'cta_text' => 'nullable|string|max:100',
            'cta_url' => 'nullable|string|max:255',
            'order' => 'nullable|integer',
            'slide_duration' => 'nullable|integer|min:2|max:30',
            'button_color' => 'nullable|string',
            'text_color' => 'nullable|string',
            'is_active' => 'nullable|boolean'
        ]);

        $data = $request->all();

        // Handle image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('hero-slides', 'public');
            $data['image'] = $imagePath;
        }

        // Set defaults
        $data['is_active'] = $request->has('is_active');
        $data['slide_duration'] = $request->slide_duration ?? 5;
        $data['button_color'] = $request->button_color ?? '#7c3aed';
        $data['text_color'] = $request->text_color ?? '#ffffff';
        $data['order'] = $request->order ?? HeroSlide::max('order') + 1;

        HeroSlide::create($data);

        return redirect()->route('super-admin.hero-slides.index')
            ->with('success', 'Hero slide created successfully!');
    }

    /**
     * Show the form for editing the specified hero slide.
     */
    public function edit(HeroSlide $heroSlide)
    {
        $slideTypes = [
            'dashboard' => '📊 Dashboard Showcase',
            'books' => '📚 Books/Library Showcase',
            'ai' => '🤖 AI Assistant Showcase',
            'community' => '🌍 Community Showcase',
            'custom' => '🎨 Custom Layout'
        ];
        return view('super-admin.hero-slides.edit', compact('heroSlide', 'slideTypes'));
    }

    /**
     * Update the specified hero slide.
     */
    public function update(Request $request, HeroSlide $heroSlide)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'slide_type' => 'required|string|in:dashboard,books,ai,community,custom',
            'badge_text' => 'nullable|string|max:100',
            'cta_text' => 'nullable|string|max:100',
            'cta_url' => 'nullable|string|max:255',
            'order' => 'nullable|integer',
            'slide_duration' => 'nullable|integer|min:2|max:30',
            'button_color' => 'nullable|string',
            'text_color' => 'nullable|string',
            'is_active' => 'nullable|boolean'
        ]);

        $data = $request->all();

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($heroSlide->image && Storage::disk('public')->exists($heroSlide->image)) {
                Storage::disk('public')->delete($heroSlide->image);
            }
            
            $imagePath = $request->file('image')->store('hero-slides', 'public');
            $data['image'] = $imagePath;
        }

        // Set defaults
        $data['is_active'] = $request->has('is_active');
        $data['slide_duration'] = $request->slide_duration ?? 5;

        $heroSlide->update($data);

        return redirect()->route('super-admin.hero-slides.index')
            ->with('success', 'Hero slide updated successfully!');
    }

    /**
     * Remove the specified hero slide.
     */
    public function destroy(HeroSlide $heroSlide)
    {
        // Delete image
        if ($heroSlide->image && Storage::disk('public')->exists($heroSlide->image)) {
            Storage::disk('public')->delete($heroSlide->image);
        }

        $heroSlide->delete();

        return redirect()->route('super-admin.hero-slides.index')
            ->with('success', 'Hero slide deleted successfully!');
    }

    /**
     * Toggle active status of hero slide.
     */
    public function toggleStatus(HeroSlide $heroSlide)
    {
        $heroSlide->is_active = !$heroSlide->is_active;
        $heroSlide->save();

        return redirect()->route('super-admin.hero-slides.index')
            ->with('success', 'Hero slide status updated!');
    }

    /**
     * Reorder hero slides.
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'slides' => 'required|array',
            'slides.*' => 'exists:hero_slides,id'
        ]);

        foreach ($request->slides as $index => $slideId) {
            HeroSlide::where('id', $slideId)->update(['order' => $index + 1]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Preview a hero slide (for Super Admin preview).
     */
    public function preview(HeroSlide $heroSlide)
    {
        return view('super-admin.hero-slides.preview', compact('heroSlide'));
    }

    /**
     * Duplicate a hero slide.
     */
    public function duplicate(HeroSlide $heroSlide)
    {
        $newSlide = $heroSlide->replicate();
        $newSlide->title = $heroSlide->title . ' (Copy)';
        $newSlide->order = HeroSlide::max('order') + 1;
        $newSlide->is_active = false;
        $newSlide->save();

        return redirect()->route('super-admin.hero-slides.index')
            ->with('success', 'Hero slide duplicated successfully!');
    }
}