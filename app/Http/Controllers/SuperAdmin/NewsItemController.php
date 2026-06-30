<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\NewsItem;
use Illuminate\Http\Request;
use Carbon\Carbon;

class NewsItemController extends Controller
{
    /**
     * Display a listing of news items.
     */
    public function index()
    {
        $newsItems = NewsItem::ordered()->get();
        return view('super-admin.news-items.index', compact('newsItems'));
    }

    /**
     * Show the form for creating a new news item.
     */
    public function create()
    {
        $categories = ['Books', 'Events', 'Certificates', 'Announcements', 'Authors'];
        return view('super-admin.news-items.create', compact('categories'));
    }

    /**
     * Store a newly created news item.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'link' => 'nullable|string|max:255',
            'is_featured' => 'nullable|boolean',
            'published_at' => 'required|date',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean'
        ]);

        $data = $request->all();
        $data['is_featured'] = $request->has('is_featured');
        $data['is_active'] = $request->has('is_active');
        $data['order'] = $request->order ?? 0;

        NewsItem::create($data);

        return redirect()->route('super-admin.news-items.index')
            ->with('success', 'News item created successfully!');
    }

    /**
     * Show the form for editing the specified news item.
     */
    public function edit(NewsItem $newsItem)
    {
        $categories = ['Books', 'Events', 'Certificates', 'Announcements', 'Authors'];
        return view('super-admin.news-items.edit', compact('newsItem', 'categories'));
    }

    /**
     * Update the specified news item.
     */
    public function update(Request $request, NewsItem $newsItem)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'link' => 'nullable|string|max:255',
            'is_featured' => 'nullable|boolean',
            'published_at' => 'required|date',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean'
        ]);

        $data = $request->all();
        $data['is_featured'] = $request->has('is_featured');
        $data['is_active'] = $request->has('is_active');

        $newsItem->update($data);

        return redirect()->route('super-admin.news-items.index')
            ->with('success', 'News item updated successfully!');
    }

    /**
     * Remove the specified news item.
     */
    public function destroy(NewsItem $newsItem)
    {
        $newsItem->delete();

        return redirect()->route('super-admin.news-items.index')
            ->with('success', 'News item deleted successfully!');
    }

    /**
     * Toggle featured status of news item.
     */
    public function toggleFeatured(NewsItem $newsItem)
    {
        $newsItem->is_featured = !$newsItem->is_featured;
        $newsItem->save();

        return redirect()->route('super-admin.news-items.index')
            ->with('success', 'News item featured status updated!');
    }

    /**
     * Toggle active status of news item.
     */
    public function toggleStatus(NewsItem $newsItem)
    {
        $newsItem->is_active = !$newsItem->is_active;
        $newsItem->save();

        return redirect()->route('super-admin.news-items.index')
            ->with('success', 'News item status updated!');
    }

    /**
     * Reorder news items.
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*' => 'exists:news_items,id'
        ]);

        foreach ($request->items as $index => $itemId) {
            NewsItem::where('id', $itemId)->update(['order' => $index + 1]);
        }

        return response()->json(['success' => true]);
    }
}