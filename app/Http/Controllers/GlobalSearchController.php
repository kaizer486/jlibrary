<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\ChatSession;
use App\Models\Certificate;
use App\Models\Quiz;
use App\Models\Group;
use App\Models\MarketplaceListing;
use App\Models\Document;
use App\Models\Transaction;
use App\Models\Institution;
use App\Models\Shelf;
use App\Models\Category;
use App\Models\Borrowing;
use App\Models\Purchase;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class GlobalSearchController extends Controller
{
    public function api(Request $request)
    {
        try {
            $query = $request->get('q', '');
            
            // Log the search query
            Log::info('Search query received: ' . $query);
            
            if (strlen($query) < 2) {
                return response()->json(['results' => []]);
            }
            
            $results = [];
            $userId = Auth::id();
            
            // ==========================================
            // 1. SEARCH BOOKS
            // ==========================================
            try {
                $books = Book::where('status', 'approved')
                    ->where(function($q) use ($query) {
                        $q->where('title', 'LIKE', "%{$query}%")
                          ->orWhere('author', 'LIKE', "%{$query}%")
                          ->orWhere('description', 'LIKE', "%{$query}%");
                    })
                    ->limit(8)
                    ->get();
                
                foreach ($books as $item) {
                    $results[] = [
                        'type' => 'book',
                        'icon' => 'ti ti-book',
                        'title' => $item->title,
                        'subtitle' => 'by ' . ($item->author ?? 'Unknown'),
                        'badge' => $item->category ?? 'Book',
                        'url' => route('library.show', $item->id),
                    ];
                }
            } catch (\Exception $e) {
                Log::error('Book search error: ' . $e->getMessage());
            }
            
            // ==========================================
            // 2. SEARCH AI CHATS
            // ==========================================
            try {
                if (class_exists('App\Models\ChatSession')) {
                    $chats = ChatSession::where('user_id', $userId)
                        ->where('title', 'LIKE', "%{$query}%")
                        ->limit(5)
                        ->get();
                    
                    foreach ($chats as $item) {
                        $results[] = [
                            'type' => 'chat',
                            'icon' => 'ti ti-message-2',
                            'title' => $item->title,
                            'subtitle' => 'AI Conversation',
                            'badge' => 'Chat',
                            'url' => route('ai.chat', ['chat_session' => $item->id])
                        ];
                    }
                }
            } catch (\Exception $e) {}
            
            // ==========================================
            // 3. SEARCH CERTIFICATES
            // ==========================================
            try {
                if (class_exists('App\Models\Certificate')) {
                    $certificates = Certificate::where('user_id', $userId)
                        ->where(function($q) use ($query) {
                            $q->where('title', 'LIKE', "%{$query}%")
                              ->orWhere('description', 'LIKE', "%{$query}%");
                        })
                        ->limit(5)
                        ->get();
                    
                    foreach ($certificates as $item) {
                        $results[] = [
                            'type' => 'certificate',
                            'icon' => 'ti ti-certificate',
                            'title' => $item->title,
                            'subtitle' => 'Earned Certificate',
                            'badge' => 'Certificate',
                            'url' => route('certificates.show', $item->id)
                        ];
                    }
                }
            } catch (\Exception $e) {}
            
            // ==========================================
            // 4. SEARCH QUIZZES
            // ==========================================
            try {
                if (class_exists('App\Models\Quiz')) {
                    $quizzes = Quiz::where(function($q) use ($query) {
                            $q->where('title', 'LIKE', "%{$query}%")
                              ->orWhere('description', 'LIKE', "%{$query}%");
                        })
                        ->limit(5)
                        ->get();
                    
                    foreach ($quizzes as $item) {
                        $results[] = [
                            'type' => 'quiz',
                            'icon' => 'ti ti-file-question',
                            'title' => $item->title,
                            'subtitle' => 'Available Quiz',
                            'badge' => 'Quiz',
                            'url' => route('quizzes.show', $item->id)
                        ];
                    }
                }
            } catch (\Exception $e) {}
            
            // ==========================================
            // 5. SEARCH GROUPS
            // ==========================================
            try {
                if (class_exists('App\Models\Group')) {
                    $groups = Group::where(function($q) use ($query) {
                            $q->where('name', 'LIKE', "%{$query}%")
                              ->orWhere('description', 'LIKE', "%{$query}%");
                        })
                        ->limit(5)
                        ->get();
                    
                    foreach ($groups as $item) {
                        $results[] = [
                            'type' => 'group',
                            'icon' => 'ti ti-users',
                            'title' => $item->name,
                            'subtitle' => 'Community Group',
                            'badge' => 'Group',
                            'url' => route('community.show', $item->id)
                        ];
                    }
                }
            } catch (\Exception $e) {}
            
            // ==========================================
            // 6. SEARCH MARKETPLACE
            // ==========================================
            try {
                if (class_exists('App\Models\MarketplaceListing')) {
                    $listings = MarketplaceListing::where('status', 'active')
                        ->where(function($q) use ($query) {
                            $q->where('title', 'LIKE', "%{$query}%")
                              ->orWhere('description', 'LIKE', "%{$query}%");
                        })
                        ->limit(5)
                        ->get();
                    
                    foreach ($listings as $item) {
                        $results[] = [
                            'type' => 'marketplace',
                            'icon' => 'ti ti-shopping-cart',
                            'title' => $item->title,
                            'subtitle' => 'TSh ' . number_format($item->price ?? 0, 2),
                            'badge' => 'Marketplace',
                            'url' => route('marketplace.show', $item->id)
                        ];
                    }
                }
            } catch (\Exception $e) {}
            
            // ==========================================
            // 7. SEARCH DOCUMENTS
            // ==========================================
            try {
                if (class_exists('App\Models\Document')) {
                    $documents = Document::where('user_id', $userId)
                        ->where(function($q) use ($query) {
                            $q->where('title', 'LIKE', "%{$query}%")
                              ->orWhere('file_name', 'LIKE', "%{$query}%");
                        })
                        ->limit(5)
                        ->get();
                    
                    foreach ($documents as $item) {
                        $results[] = [
                            'type' => 'document',
                            'icon' => 'ti ti-file',
                            'title' => $item->title ?? $item->file_name,
                            'subtitle' => 'Your Document',
                            'badge' => 'Document',
                            'url' => route('documents.show', $item->id)
                        ];
                    }
                }
            } catch (\Exception $e) {}
            
            // ==========================================
            // 8. SEARCH INSTITUTIONS
            // ==========================================
            try {
                if (class_exists('App\Models\Institution')) {
                    $institutions = Institution::where(function($q) use ($query) {
                            $q->where('name', 'LIKE', "%{$query}%")
                              ->orWhere('description', 'LIKE', "%{$query}%")
                              ->orWhere('city', 'LIKE', "%{$query}%");
                        })
                        ->limit(5)
                        ->get();
                    
                    foreach ($institutions as $item) {
                        $results[] = [
                            'type' => 'institution',
                            'icon' => 'ti ti-building',
                            'title' => $item->name,
                            'subtitle' => ($item->city ?? '') . ' ' . ($item->region ?? ''),
                            'badge' => 'Institution',
                            'url' => route('institutions.show', $item->id)
                        ];
                    }
                }
            } catch (\Exception $e) {}
            
            // ==========================================
            // 9. SEARCH TRANSACTIONS
            // ==========================================
            try {
                if (class_exists('App\Models\Transaction')) {
                    $transactions = Transaction::where('user_id', $userId)
                        ->where(function($q) use ($query) {
                            $q->where('description', 'LIKE', "%{$query}%")
                              ->orWhere('type', 'LIKE', "%{$query}%");
                        })
                        ->limit(5)
                        ->get();
                    
                    foreach ($transactions as $item) {
                        $results[] = [
                            'type' => 'transaction',
                            'icon' => 'ti ti-wallet',
                            'title' => $item->description,
                            'subtitle' => ucfirst($item->type) . ' - TSh ' . number_format($item->amount, 2),
                            'badge' => $item->status ?? 'Transaction',
                            'url' => route('wallet.history')
                        ];
                    }
                }
            } catch (\Exception $e) {}
            
            // ==========================================
            // 10. SEARCH NOTIFICATIONS
            // ==========================================
            try {
                if (class_exists('App\Models\Notification')) {
                    $notifications = Notification::where('user_id', $userId)
                        ->where(function($q) use ($query) {
                            $q->where('title', 'LIKE', "%{$query}%")
                              ->orWhere('message', 'LIKE', "%{$query}%");
                        })
                        ->limit(5)
                        ->get();
                    
                    foreach ($notifications as $item) {
                        $results[] = [
                            'type' => 'notification',
                            'icon' => 'ti ti-bell',
                            'title' => $item->title ?? 'Notification',
                            'subtitle' => $item->message ?? '',
                            'badge' => $item->type ?? 'Alert',
                            'url' => route('notifications.index')
                        ];
                    }
                }
            } catch (\Exception $e) {}
            
            // Log results count
            Log::info('Total results found: ' . count($results));
            
            // Sort results with books first
            usort($results, function($a, $b) {
                $priority = [
                    'book' => 1,
                    'institution' => 2,
                    'group' => 3,
                    'marketplace' => 4,
                    'quiz' => 5,
                    'certificate' => 6,
                ];
                
                $pa = $priority[$a['type']] ?? 99;
                $pb = $priority[$b['type']] ?? 99;
                
                return $pa <=> $pb;
            });
            
            // Limit results
            $results = array_slice($results, 0, 30);
            
            return response()->json([
                'results' => $results,
                'debug' => [
                    'query' => $query,
                    'total' => count($results)
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Global search error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'results' => [],
                'error' => $e->getMessage()
            ], 500);
        }
    }
    /**
 * Display the global search results page
 */
public function index(Request $request)
{
    $query = $request->get('q', '');
    $results = [];
    
    if (strlen($query) >= 2) {
        // Get all results (not just limited)
        $userId = Auth::id();
        
        // Search Books
        try {
            $books = Book::where('status', 'approved')
                ->where(function($q) use ($query) {
                    $q->where('title', 'LIKE', "%{$query}%")
                      ->orWhere('author', 'LIKE', "%{$query}%")
                      ->orWhere('description', 'LIKE', "%{$query}%");
                })
                ->get();
            
            foreach ($books as $item) {
                $results[] = [
                    'type' => 'book',
                    'icon' => 'ti ti-book',
                    'title' => $item->title,
                    'subtitle' => 'by ' . ($item->author ?? 'Unknown'),
                    'badge' => $item->category ?? 'Book',
                    'url' => route('library.show', $item->id),
                ];
            }
        } catch (\Exception $e) {}
        
        // Search Quizzes
        try {
            if (class_exists('App\Models\Quiz')) {
                $quizzes = Quiz::where(function($q) use ($query) {
                        $q->where('title', 'LIKE', "%{$query}%")
                          ->orWhere('description', 'LIKE', "%{$query}%");
                    })
                    ->get();
                
                foreach ($quizzes as $item) {
                    $results[] = [
                        'type' => 'quiz',
                        'icon' => 'ti ti-file-question',
                        'title' => $item->title,
                        'subtitle' => 'Available Quiz',
                        'badge' => 'Quiz',
                        'url' => route('quizzes.show', $item->id)
                    ];
                }
            }
        } catch (\Exception $e) {}
        
        // Search Groups
        try {
            if (class_exists('App\Models\Group')) {
                $groups = Group::where(function($q) use ($query) {
                        $q->where('name', 'LIKE', "%{$query}%")
                          ->orWhere('description', 'LIKE', "%{$query}%");
                    })
                    ->get();
                
                foreach ($groups as $item) {
                    $results[] = [
                        'type' => 'group',
                        'icon' => 'ti ti-users',
                        'title' => $item->name,
                        'subtitle' => 'Community Group',
                        'badge' => 'Group',
                        'url' => route('community.show', $item->id)
                    ];
                }
            }
        } catch (\Exception $e) {}
        
        // Search Institutions
        try {
            if (class_exists('App\Models\Institution')) {
                $institutions = Institution::where(function($q) use ($query) {
                        $q->where('name', 'LIKE', "%{$query}%")
                          ->orWhere('description', 'LIKE', "%{$query}%")
                          ->orWhere('city', 'LIKE', "%{$query}%");
                    })
                    ->get();
                
                foreach ($institutions as $item) {
                    $results[] = [
                        'type' => 'institution',
                        'icon' => 'ti ti-building',
                        'title' => $item->name,
                        'subtitle' => ($item->city ?? '') . ' ' . ($item->region ?? ''),
                        'badge' => 'Institution',
                        'url' => route('institutions.show', $item->id)
                    ];
                }
            }
        } catch (\Exception $e) {}
        
        // Search Marketplace
        try {
            if (class_exists('App\Models\MarketplaceListing')) {
                $listings = MarketplaceListing::where('status', 'active')
                    ->where(function($q) use ($query) {
                        $q->where('title', 'LIKE', "%{$query}%")
                          ->orWhere('description', 'LIKE', "%{$query}%");
                    })
                    ->get();
                
                foreach ($listings as $item) {
                    $results[] = [
                        'type' => 'marketplace',
                        'icon' => 'ti ti-shopping-cart',
                        'title' => $item->title,
                        'subtitle' => 'TSh ' . number_format($item->price ?? 0, 2),
                        'badge' => 'Marketplace',
                        'url' => route('marketplace.show', $item->id)
                    ];
                }
            }
        } catch (\Exception $e) {}
        
        // Search Certificates
        try {
            if (class_exists('App\Models\Certificate')) {
                $certificates = Certificate::where('user_id', $userId)
                    ->where(function($q) use ($query) {
                        $q->where('title', 'LIKE', "%{$query}%")
                          ->orWhere('description', 'LIKE', "%{$query}%");
                    })
                    ->get();
                
                foreach ($certificates as $item) {
                    $results[] = [
                        'type' => 'certificate',
                        'icon' => 'ti ti-certificate',
                        'title' => $item->title,
                        'subtitle' => 'Earned Certificate',
                        'badge' => 'Certificate',
                        'url' => route('certificates.show', $item->id)
                    ];
                }
            }
        } catch (\Exception $e) {}
        
        // Search Documents
        try {
            if (class_exists('App\Models\Document')) {
                $documents = Document::where('user_id', $userId)
                    ->where(function($q) use ($query) {
                        $q->where('title', 'LIKE', "%{$query}%")
                          ->orWhere('file_name', 'LIKE', "%{$query}%");
                    })
                    ->get();
                
                foreach ($documents as $item) {
                    $results[] = [
                        'type' => 'document',
                        'icon' => 'ti ti-file',
                        'title' => $item->title ?? $item->file_name,
                        'subtitle' => 'Your Document',
                        'badge' => 'Document',
                        'url' => route('documents.show', $item->id)
                    ];
                }
            }
        } catch (\Exception $e) {}
        
        // Search Notifications
        try {
            if (class_exists('App\Models\Notification')) {
                $notifications = Notification::where('user_id', $userId)
                    ->where(function($q) use ($query) {
                        $q->where('title', 'LIKE', "%{$query}%")
                          ->orWhere('message', 'LIKE', "%{$query}%");
                    })
                    ->get();
                
                foreach ($notifications as $item) {
                    $results[] = [
                        'type' => 'notification',
                        'icon' => 'ti ti-bell',
                        'title' => $item->title ?? 'Notification',
                        'subtitle' => $item->message ?? '',
                        'badge' => $item->type ?? 'Alert',
                        'url' => route('notifications.index')
                    ];
                }
            }
        } catch (\Exception $e) {}
    }
    
    // Group results by type for the view
    $groupedResults = [];
    foreach ($results as $item) {
        $type = $item['type'];
        if (!isset($groupedResults[$type])) {
            $groupedResults[$type] = [];
        }
        $groupedResults[$type][] = $item;
    }
    
    return view('global-search.index', [
        'query' => $query,
        'results' => $results,
        'groupedResults' => $groupedResults,
        'totalResults' => count($results)
    ]);
}
}