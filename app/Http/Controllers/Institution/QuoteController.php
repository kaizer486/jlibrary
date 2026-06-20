<?php

namespace App\Http\Controllers\Institution;

use App\Http\Controllers\Controller;
use App\Models\Quote;
use App\Models\Institution;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class QuoteController extends Controller
{
    /**
     * Display a listing of quotes for the institution.
     */
    public function index(Request $request)
    {
        $institution = auth()->user()->institution;
        
        if (!$institution) {
            abort(403, 'You do not belong to any institution.');
        }
        
        // Build query
        $query = Quote::where('institution_id', $institution->id);
        
        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('quote_text', 'LIKE', "%{$search}%")
                  ->orWhere('author', 'LIKE', "%{$search}%");
            });
        }
        
        // Category filter
        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }
        
        // Status filter
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        
        // Get paginated results
        $quotes = $query->latest()->paginate($request->per_page ?? 15)
            ->appends($request->query());
        
        // Get statistics
        $stats = [
            'total' => Quote::where('institution_id', $institution->id)->count(),
            'active' => Quote::where('institution_id', $institution->id)->where('status', 'active')->count(),
            'saves' => Quote::where('institution_id', $institution->id)->sum('saves_count'),
            'shares' => Quote::where('institution_id', $institution->id)->sum('shares_count'),
        ];
        
        // Categories
        $categories = [
            'motivational' => 'Motivational',
            'inspirational' => 'Inspirational',
            'educational' => 'Educational',
            'leadership' => 'Leadership',
            'success' => 'Success',
            'wisdom' => 'Wisdom',
            'general' => 'General',
        ];
        
        return view('institution.quotes.index', compact('quotes', 'institution', 'stats', 'categories'));
    }
    
    /**
     * Show the form for creating a new quote.
     */
    public function create()
    {
        $institution = auth()->user()->institution;
        
        if (!$institution) {
            abort(403, 'You do not belong to any institution.');
        }
        
        if (!auth()->user()->can('create', Quote::class)) {
            abort(403, 'You do not have permission to create quotes.');
        }
        
        $categories = [
            'motivational' => 'Motivational',
            'inspirational' => 'Inspirational',
            'educational' => 'Educational',
            'leadership' => 'Leadership',
            'success' => 'Success',
            'wisdom' => 'Wisdom',
            'general' => 'General',
        ];
        
        return view('institution.quotes.create', compact('institution', 'categories'));
    }
    
    /**
     * Store a newly created quote.
     */
    public function store(Request $request)
    {
        $institution = auth()->user()->institution;
        
        if (!$institution) {
            abort(403, 'You do not belong to any institution.');
        }
        
        if (!auth()->user()->can('create', Quote::class)) {
            abort(403, 'You do not have permission to create quotes.');
        }
        
        $validated = $request->validate([
            'quote_text' => 'required|string|max:1000',
            'author' => 'nullable|string|max:255',
            'category' => 'required|string|in:motivational,inspirational,educational,leadership,success,wisdom,general',
            'status' => 'required|in:active,inactive,draft',
            'scheduled_date' => 'nullable|date|after_or_equal:today',
        ]);
        
        $quote = Quote::create([
            'institution_id' => $institution->id,
            'quote_text' => $validated['quote_text'],
            'author' => $validated['author'] ?? null,
            'category' => $validated['category'],
            'status' => $validated['status'],
            'scheduled_date' => $validated['scheduled_date'] ?? null,
            'views_count' => 0,
            'saves_count' => 0,
            'shares_count' => 0,
        ]);
        
        return redirect()->route('institution.quotes.index')
            ->with('success', 'Quote created successfully!');
    }
    
    /**
     * Display the specified quote analytics.
     */
    public function analytics(Quote $quote)
    {
        $institution = auth()->user()->institution;
        
        if (!$institution) {
            abort(403, 'You do not belong to any institution.');
        }
        
        if ($quote->institution_id !== $institution->id) {
            abort(403, 'This quote does not belong to your institution.');
        }
        
        if (!auth()->user()->can('view', $quote)) {
            abort(403, 'You do not have permission to view analytics for this quote.');
        }
        
        // Generate performance data for chart
        $performanceData = $this->getPerformanceData($quote);
        
        return view('institution.quotes.analytics', compact('quote', 'institution', 'performanceData'));
    }
    
    /**
     * Show the form for editing the specified quote.
     */
    public function edit(Quote $quote)
    {
        $institution = auth()->user()->institution;
        
        if (!$institution) {
            abort(403, 'You do not belong to any institution.');
        }
        
        if ($quote->institution_id !== $institution->id) {
            abort(403, 'This quote does not belong to your institution.');
        }
        
        if (!auth()->user()->can('update', $quote)) {
            abort(403, 'You do not have permission to edit this quote.');
        }
        
        $categories = [
            'motivational' => 'Motivational',
            'inspirational' => 'Inspirational',
            'educational' => 'Educational',
            'leadership' => 'Leadership',
            'success' => 'Success',
            'wisdom' => 'Wisdom',
            'general' => 'General',
        ];
        
        return view('institution.quotes.edit', compact('quote', 'institution', 'categories'));
    }
    
    /**
     * Update the specified quote.
     */
    public function update(Request $request, Quote $quote)
    {
        $institution = auth()->user()->institution;
        
        if (!$institution) {
            abort(403, 'You do not belong to any institution.');
        }
        
        if ($quote->institution_id !== $institution->id) {
            abort(403, 'This quote does not belong to your institution.');
        }
        
        if (!auth()->user()->can('update', $quote)) {
            abort(403, 'You do not have permission to update this quote.');
        }
        
        $validated = $request->validate([
            'quote_text' => 'required|string|max:1000',
            'author' => 'nullable|string|max:255',
            'category' => 'required|string|in:motivational,inspirational,educational,leadership,success,wisdom,general',
            'status' => 'required|in:active,inactive,draft',
            'scheduled_date' => 'nullable|date|after_or_equal:today',
        ]);
        
        $quote->update($validated);
        
        return redirect()->route('institution.quotes.index')
            ->with('success', 'Quote updated successfully!');
    }
    
    /**
     * Remove the specified quote.
     */
    public function destroy(Quote $quote)
    {
        $institution = auth()->user()->institution;
        
        if (!$institution) {
            abort(403, 'You do not belong to any institution.');
        }
        
        if ($quote->institution_id !== $institution->id) {
            abort(403, 'This quote does not belong to your institution.');
        }
        
        if (!auth()->user()->can('delete', $quote)) {
            abort(403, 'You do not have permission to delete this quote.');
        }
        
        $quote->delete();
        
        return redirect()->route('institution.quotes.index')
            ->with('success', 'Quote deleted successfully!');
    }
    
    /**
     * Toggle quote status (active/inactive).
     */
    public function toggleStatus(Quote $quote)
    {
        $institution = auth()->user()->institution;
        
        if (!$institution) {
            abort(403, 'You do not belong to any institution.');
        }
        
        if ($quote->institution_id !== $institution->id) {
            abort(403, 'This quote does not belong to your institution.');
        }
        
        if (!auth()->user()->can('update', $quote)) {
            abort(403, 'You do not have permission to update this quote.');
        }
        
        $newStatus = $quote->status === 'active' ? 'inactive' : 'active';
        $quote->update(['status' => $newStatus]);
        
        return redirect()->back()
            ->with('success', "Quote " . ($newStatus === 'active' ? 'activated' : 'deactivated') . " successfully!");
    }
    
    /**
     * Get performance data for analytics chart.
     */
    private function getPerformanceData($quote)
    {
        // This is a placeholder. In a real implementation, you would:
        // 1. Have a quote_analytics table that tracks daily views/saves/shares
        // 2. Or use a package like spatie/laravel-analytics
        // 3. Or fetch from event logs
        
        // For now, generate sample data based on the quote's age
        $days = 30;
        $dates = [];
        $views = [];
        $saves = [];
        $shares = [];
        
        $createdAt = $quote->created_at ?? now()->subDays($days);
        $daysSinceCreation = $createdAt->diffInDays(now());
        $actualDays = min($daysSinceCreation, $days);
        
        for ($i = $actualDays; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dates[] = $date->format('M d');
            
            // Generate realistic looking data
            $viewCount = max(0, rand(1, 20) + ($actualDays - $i) * 0.5);
            $views[] = round($viewCount);
            $saves[] = round($viewCount * rand(5, 30) / 100);
            $shares[] = round($viewCount * rand(2, 15) / 100);
        }
        
        return [
            'dates' => $dates,
            'views' => $views,
            'saves' => $saves,
            'shares' => $shares,
        ];
    }
    
    /**
     * Export quotes as CSV.
     */
    public function export(Request $request)
    {
        $institution = auth()->user()->institution;
        
        if (!$institution) {
            abort(403, 'You do not belong to any institution.');
        }
        
        if (!auth()->user()->can('export', Quote::class)) {
            abort(403, 'You do not have permission to export quotes.');
        }
        
        $quotes = Quote::where('institution_id', $institution->id)->get();
        
        $filename = "quotes_{$institution->slug}_{$institution->id}.csv";
        $handle = fopen('php://output', 'w');
        
        // Add headers
        fputcsv($handle, ['ID', 'Quote', 'Author', 'Category', 'Status', 'Views', 'Saves', 'Shares', 'Created At']);
        
        // Add data
        foreach ($quotes as $quote) {
            fputcsv($handle, [
                $quote->id,
                $quote->quote_text,
                $quote->author ?? 'N/A',
                $quote->category ?? 'General',
                $quote->status,
                $quote->views_count ?? 0,
                $quote->saves_count ?? 0,
                $quote->shares_count ?? 0,
                $quote->created_at->format('Y-m-d H:i:s'),
            ]);
        }
        
        fclose($handle);
        
        return response()->stream(
            function() { /* Already output */ },
            200,
            [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename={$filename}",
            ]
        );
    }
}