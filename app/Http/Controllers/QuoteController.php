<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuoteController extends Controller
{
    // Get quote of the day (for dashboard)
    public function quoteOfTheDay()
    {
        $user = Auth::user();
        $quote = Quote::getQuoteOfTheDay($user);
        $isFavorited = $quote ? $quote->favoritedBy()->where('user_id', $user->id)->exists() : false;
        
        return response()->json([
            'success' => true,
            'quote' => $quote,
            'is_favorited' => $isFavorited
        ]);
    }
    
    // Save/unsave favorite quote
    public function toggleFavorite(Quote $quote)
    {
        $user = Auth::user();
        
        if ($quote->favoritedBy()->where('user_id', $user->id)->exists()) {
            $quote->favoritedBy()->detach($user->id);
            $quote->decrement('saves_count');
            $message = 'Quote removed from favorites';
            $isFavorited = false;
        } else {
            $quote->favoritedBy()->attach($user->id);
            $quote->increment('saves_count');
            $message = 'Quote saved to favorites';
            $isFavorited = true;
        }
        
        return response()->json([
            'success' => true,
            'message' => $message,
            'is_favorited' => $isFavorited
        ]);
    }
    
    // Share quote
    public function share(Quote $quote)
    {
        $quote->increment('shares_count');
        
        return response()->json([
            'success' => true,
            'message' => 'Quote shared successfully'
        ]);
    }
    
    // Get next random quote
    public function nextQuote()
    {
        $user = Auth::user();
        $institutionId = $user->institution_id;
        
        $quote = Quote::active()
            ->where(function($q) use ($institutionId) {
                if ($institutionId) {
                    $q->where('institution_id', $institutionId);
                } else {
                    $q->whereNull('institution_id');
                }
            })
            ->inRandomOrder()
            ->first();
            
        $isFavorited = $quote ? $quote->favoritedBy()->where('user_id', $user->id)->exists() : false;
        
        return response()->json([
            'success' => true,
            'quote' => $quote,
            'is_favorited' => $isFavorited
        ]);
    }
  
    
// For Institution Admin - get their institution's quotes
public function institutionQuotes(Request $request)
{
    $user = auth()->user();
    $institutionId = $user->institution_id;
    
    if (!$institutionId) {
        return redirect()->back()->with('error', 'You are not associated with any institution.');
    }
    
    $query = Quote::where('institution_id', $institutionId)->with('creator');
    
    if ($request->search) {
        $query->where(function($q) use ($request) {
            $q->where('quote_text', 'like', '%' . $request->search . '%')
              ->orWhere('author', 'like', '%' . $request->search . '%');
        });
    }
    
    if ($request->category && $request->category !== 'all') {
        $query->where('category', $request->category);
    }
    
    if ($request->status && $request->status !== 'all') {
        $query->where('status', $request->status);
    }
    
    $quotes = $query->latest()->paginate(15);
    $categories = Quote::getCategories();
    
    return view('institution.quotes.index', compact('quotes', 'categories'));
}

// For Institution Admin - create quote for their institution
public function institutionCreate()
{
    $categories = Quote::getCategories();
    $user = auth()->user();
    $institution = $user->institution;
    
    return view('institution.quotes.create', compact('categories', 'user', 'institution'));
}

// For Institution Admin - store quote for their institution
public function institutionStore(Request $request)
{
    $user = auth()->user();
    $institutionId = $user->institution_id;
    
    if (!$institutionId) {
        return redirect()->back()->with('error', 'You are not associated with any institution.');
    }
    
    $request->validate([
        'quote_text' => 'required|string|min:10',
        'author' => 'nullable|string|max:255',
        'category' => 'required|string',
        'status' => 'required|in:active,inactive,draft',
        'scheduled_date' => 'nullable|date'
    ]);
    
    Quote::create([
        'quote_text' => $request->quote_text,
        'author' => $request->author,
        'category' => $request->category,
        'status' => $request->status,
        'scheduled_date' => $request->scheduled_date,
        'institution_id' => $institutionId,
        'created_by' => auth()->id()
    ]);
    
    return redirect()->route('institution.quotes.index')
        ->with('success', 'Quote added to your institution successfully!');
}
    // ========== ADMIN METHODS ==========
    
    // Display all quotes (admin)
   public function index(Request $request)
{
    $user = auth()->user();
    $quotes = Quote::getAdminQuotes($user, $request);
    $categories = Quote::getCategories();
    
    // Determine which view to use based on role
    if ($user->role === 'super_admin') {
        return view('super-admin.quotes.index', compact('quotes', 'categories'));
    }
    
    return view('admin.quotes.index', compact('quotes', 'categories'));
}
    
    // Show create quote form
  public function create()
{
    $categories = Quote::getCategories();
    $user = auth()->user();
    
    // Determine which view to use based on role
    if ($user->role === 'super_admin') {
        return view('super-admin.quotes.create', compact('categories', 'user'));
    }
    
    return view('admin.quotes.create', compact('categories', 'user'));
}
    // Store new quote
public function store(Request $request)
{
    $request->validate([
        'quote_text' => 'required|string|min:10',
        'author' => 'nullable|string|max:255',
        'category' => 'required|string',
        'status' => 'required|in:active,inactive,draft',
        'scheduled_date' => 'nullable|date'
    ]);
    
    $user = auth()->user();
    $institutionId = null;
    
    // Institution admin quotes go to their institution
    if ($user->role === 'institution_admin') {
        if (!$user->institution_id) {
            return redirect()->back()->with('error', 'You are not associated with any institution.');
        }
        $institutionId = $user->institution_id;
    }
    // Super admin and admin create global quotes (institution_id = null)
    
    Quote::create([
        'quote_text' => $request->quote_text,
        'author' => $request->author,
        'category' => $request->category,
        'status' => $request->status,
        'scheduled_date' => $request->scheduled_date,
        'institution_id' => $institutionId,
        'created_by' => auth()->id()
    ]);
    
    $message = $institutionId 
        ? 'Quote added to your institution successfully!' 
        : 'Global quote added successfully!';
    
    // Redirect based on role
    if ($user->role === 'super_admin') {
        return redirect()->route('super-admin.quotes.index')->with('success', $message);
    }
    
    return redirect()->route('admin.quotes.index')->with('success', $message);
}    
    // Show edit form
public function edit(Quote $quote)
{
    $user = auth()->user();
    $categories = Quote::getCategories();
    
    // Institution admin can only edit their institution quotes
    if ($user->role === 'institution_admin' && $quote->institution_id !== $user->institution_id) {
        abort(403, 'You can only edit quotes from your institution.');
    }
    
    // Super admin/admin can only edit global quotes
    if (($user->role === 'super_admin' || $user->role === 'admin') && $quote->institution_id !== null) {
        abort(403, 'You can only edit global quotes.');
    }
    
    // Determine which view to use based on role
    if ($user->role === 'super_admin') {
        return view('super-admin.quotes.edit', compact('quote', 'categories', 'user'));
    }
    
    return view('admin.quotes.edit', compact('quote', 'categories', 'user'));
}
    
    // Update quote
   public function update(Request $request, Quote $quote)
{
    $user = auth()->user();
    
    // Permission check
    if ($user->role === 'institution_admin' && $quote->institution_id !== $user->institution_id) {
        abort(403);
    }
    if (($user->role === 'super_admin' || $user->role === 'admin') && $quote->institution_id !== null) {
        abort(403);
    }
    
    $request->validate([
        'quote_text' => 'required|string|min:10',
        'author' => 'nullable|string|max:255',
        'category' => 'required|string',
        'status' => 'required|in:active,inactive,draft',
        'scheduled_date' => 'nullable|date'
    ]);
    
    $quote->update($request->only([
        'quote_text', 'author', 'category', 'status', 'scheduled_date'
    ]));
    
    // Redirect based on role
    if ($user->role === 'super_admin') {
        return redirect()->route('super-admin.quotes.index')->with('success', 'Quote updated successfully!');
    }
    
    return redirect()->route('admin.quotes.index')->with('success', 'Quote updated successfully!');
}
    
    // Delete quote
    public function destroy(Quote $quote)
{
    $user = auth()->user();
    
    // Permission check
    if ($user->role === 'institution_admin' && $quote->institution_id !== $user->institution_id) {
        abort(403);
    }
    if (($user->role === 'super_admin' || $user->role === 'admin') && $quote->institution_id !== null) {
        abort(403);
    }
    
    $quote->delete();
    
    // Redirect based on role
    if ($user->role === 'super_admin') {
        return redirect()->route('super-admin.quotes.index')->with('success', 'Quote deleted successfully!');
    }
    
    return redirect()->route('admin.quotes.index')->with('success', 'Quote deleted successfully!');
}
    
    // Show quote analytics
public function analytics(Quote $quote)
{
    $user = auth()->user();
    
    // Permission check
    if ($user->role === 'institution_admin' && $quote->institution_id !== $user->institution_id) {
        abort(403);
    }
    if (($user->role === 'super_admin' || $user->role === 'admin') && $quote->institution_id !== null) {
        abort(403);
    }
    
    // Determine which view to use based on role
    if ($user->role === 'super_admin') {
        return view('super-admin.quotes.analytics', compact('quote'));
    }
    
    return view('admin.quotes.analytics', compact('quote'));
}
}