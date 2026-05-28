<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = Book::query()->where('status', 'approved');

        // Search by keyword (title, author, description)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('author', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by price
        if ($request->filled('price')) {
            if ($request->price === 'free') {
                $query->where('is_paid', false);
            } elseif ($request->price === 'paid') {
                $query->where('is_paid', true);
            }
        }

        // Filter by price range (min - max)
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Filter by rating
        if ($request->filled('rating')) {
            $rating = $request->rating;
            $query->withAvg('ratings', 'rating')
                  ->having('ratings_avg_rating', '>=', $rating);
        }

        // Sort options
        switch ($request->sort) {
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'popular':
                $query->orderBy('downloads', 'desc');
                break;
            case 'rating_high':
                $query->withAvg('ratings', 'rating')
                      ->orderBy('ratings_avg_rating', 'desc');
                break;
            default:
                $query->latest();
                break;
        }

        $books = $query->paginate(12)->withQueryString();

        // Get filter counts for sidebar
        $totalBooks = Book::where('status', 'approved')->count();
        $freeBooks = Book::where('status', 'approved')->where('is_paid', false)->count();
        $paidBooks = Book::where('status', 'approved')->where('is_paid', true)->count();

        // Get price range for slider
        $minPrice = Book::where('status', 'approved')->where('is_paid', true)->min('price') ?? 0;
        $maxPrice = Book::where('status', 'approved')->where('is_paid', true)->max('price') ?? 100000;

        return view('search.index', compact('books', 'totalBooks', 'freeBooks', 'paidBooks', 'minPrice', 'maxPrice'));
    }

    // AJAX search for live results
    public function live(Request $request)
    {
        $search = $request->get('q');
        
        $books = Book::where('status', 'approved')
            ->where(function($query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                      ->orWhere('author', 'like', "%{$search}%");
            })
            ->limit(5)
            ->get();

        return response()->json($books);
    }

    // Advanced filters (AJAX)
    public function filter(Request $request)
    {
        $query = Book::query()->where('status', 'approved');

        if ($request->filled('search')) {
            $query->where('title', 'like', "%{$request->search}%");
        }

        if ($request->filled('price')) {
            if ($request->price === 'free') {
                $query->where('is_paid', false);
            } elseif ($request->price === 'paid') {
                $query->where('is_paid', true);
            }
        }

        if ($request->filled('rating')) {
            $query->withAvg('ratings', 'rating')
                  ->having('ratings_avg_rating', '>=', $request->rating);
        }

        $books = $query->latest()->limit(12)->get();

        return response()->json($books);
    }
}