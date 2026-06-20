<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InstitutionQuoteController extends Controller
{
  
    public function index(Request $request)
    {
        $user = auth()->user();
        $institutionId = $user->institution_id;
        
        $query = Quote::where('institution_id', $institutionId);
        
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
    
    public function create()
    {
        $categories = Quote::getCategories();
        $institution = auth()->user()->institution;
        
        return view('institution.quotes.create', compact('categories', 'institution'));
    }
    
    public function store(Request $request)
    {
        $user = auth()->user();
        $institutionId = $user->institution_id;
        
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
    
    public function edit(Quote $quote)
    {
        $user = auth()->user();
        
        if ($quote->institution_id !== $user->institution_id) {
            abort(403, 'You can only edit quotes from your institution.');
        }
        
        $categories = Quote::getCategories();
        return view('institution.quotes.edit', compact('quote', 'categories'));
    }
    
    public function update(Request $request, Quote $quote)
    {
        $user = auth()->user();
        
        if ($quote->institution_id !== $user->institution_id) {
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
        
        return redirect()->route('institution.quotes.index')
            ->with('success', 'Quote updated successfully!');
    }
    
    public function destroy(Quote $quote)
    {
        $user = auth()->user();
        
        if ($quote->institution_id !== $user->institution_id) {
            abort(403);
        }
        
        $quote->delete();
        
        return redirect()->route('institution.quotes.index')
            ->with('success', 'Quote deleted successfully!');
    }
    
    public function analytics(Quote $quote)
    {
        $user = auth()->user();
        
        if ($quote->institution_id !== $user->institution_id) {
            abort(403);
        }
        
        return view('institution.quotes.analytics', compact('quote'));
    }
}