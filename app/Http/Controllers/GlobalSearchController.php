<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\ChatSession;
use App\Models\Certificate;
use App\Models\Quiz;
use App\Models\User;
use App\Models\Group;
use App\Models\MarketplaceListing;
use App\Models\Document;
use App\Models\Transaction;
use App\Models\Referral;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GlobalSearchController extends Controller
{
    public function api(Request $request)
    {
        $query = $request->get('q', '');
        
        if (strlen($query) < 2) {
            return response()->json(['results' => []]);
        }
        
        $results = [];
        
        // 1. Search Books (Library)
        try {
            $books = Book::where('status', 'approved')
                ->where(function($q) use ($query) {
                    $q->where('title', 'LIKE', "%{$query}%")
                      ->orWhere('author', 'LIKE', "%{$query}%");
                })
                ->limit(3)
                ->get();
            
            foreach ($books as $item) {
                $results[] = [
                    'type' => 'book',
                    'title' => $item->title,
                    'subtitle' => 'by ' . ($item->author ?? 'Unknown'),
                    'url' => route('library.show', $item->id)
                ];
            }
        } catch (\Exception $e) {}
        
        // 2. Search AI Chats
        try {
            $chats = ChatSession::where('user_id', Auth::id())
                ->where('title', 'LIKE', "%{$query}%")
                ->limit(3)
                ->get();
            
            foreach ($chats as $item) {
                $results[] = [
                    'type' => 'chat',
                    'title' => $item->title,
                    'subtitle' => 'AI Conversation',
                    'url' => route('ai.chat', ['chat_session' => $item->id])
                ];
            }
        } catch (\Exception $e) {}
        
        // 3. Search Certificates
        try {
            $certificates = Certificate::where('user_id', Auth::id())
                ->where('title', 'LIKE', "%{$query}%")
                ->limit(3)
                ->get();
            
            foreach ($certificates as $item) {
                $results[] = [
                    'type' => 'certificate',
                    'title' => $item->title,
                    'subtitle' => 'Earned Certificate',
                    'url' => route('certificates.show', $item->id)
                ];
            }
        } catch (\Exception $e) {}
        
        // 4. Search Quizzes
        try {
            $quizzes = Quiz::where('title', 'LIKE', "%{$query}%")
                ->limit(3)
                ->get();
            
            foreach ($quizzes as $item) {
                $results[] = [
                    'type' => 'quiz',
                    'title' => $item->title,
                    'subtitle' => 'Available Quiz',
                    'url' => route('quizzes.show', $item->id)
                ];
            }
        } catch (\Exception $e) {}
        
        // 5. Search Users (Community)
        try {
            $users = User::where('full_name', 'LIKE', "%{$query}%")
                ->limit(3)
                ->get();
            
            foreach ($users as $item) {
                $results[] = [
                    'type' => 'user',
                    'title' => $item->full_name,
                    'subtitle' => $item->email,
                    'url' => route('profile.show', $item->id)
                ];
            }
        } catch (\Exception $e) {}
        
        // 6. Search Community Groups
        try {
            $groups = Group::where('name', 'LIKE', "%{$query}%")
                ->limit(3)
                ->get();
            
            foreach ($groups as $item) {
                $results[] = [
                    'type' => 'group',
                    'title' => $item->name,
                    'subtitle' => 'Community Group',
                    'url' => route('community.show', $item->id)
                ];
            }
        } catch (\Exception $e) {}
        
        // 7. Search Marketplace Listings
        try {
            $listings = MarketplaceListing::where('title', 'LIKE', "%{$query}%")
                ->limit(3)
                ->get();
            
            foreach ($listings as $item) {
                $results[] = [
                    'type' => 'marketplace',
                    'title' => $item->title,
                    'subtitle' => 'TSh ' . number_format($item->price ?? 0, 2),
                    'url' => route('marketplace.show', $item->id)
                ];
            }
        } catch (\Exception $e) {}
        
        // 8. Search User Documents
        try {
            $documents = Document::where('user_id', Auth::id())
                ->where('title', 'LIKE', "%{$query}%")
                ->limit(3)
                ->get();
            
            foreach ($documents as $item) {
                $results[] = [
                    'type' => 'document',
                    'title' => $item->title ?? $item->file_name,
                    'subtitle' => 'Your Document',
                    'url' => route('documents.show', $item->id)
                ];
            }
        } catch (\Exception $e) {}
        
        // 9. Search Wallet Transactions
        try {
            $transactions = Transaction::where('user_id', Auth::id())
                ->where('description', 'LIKE', "%{$query}%")
                ->limit(3)
                ->get();
            
            foreach ($transactions as $item) {
                $results[] = [
                    'type' => 'transaction',
                    'title' => $item->description,
                    'subtitle' => $item->type . ' - TSh ' . number_format($item->amount, 2),
                    'url' => route('wallet.history')
                ];
            }
        } catch (\Exception $e) {}
        
        // 10. Search Referrals
        try {
            $referrals = Referral::where('referrer_id', Auth::id())
                ->where(function($q) use ($query) {
                    $q->where('code', 'LIKE', "%{$query}%")
                      ->orWhere('status', 'LIKE', "%{$query}%");
                })
                ->limit(3)
                ->get();
            
            foreach ($referrals as $item) {
                $results[] = [
                    'type' => 'referral',
                    'title' => 'Referral: ' . $item->code,
                    'subtitle' => 'Status: ' . ucfirst($item->status),
                    'url' => route('referrals.index')
                ];
            }
        } catch (\Exception $e) {}
        
        // 11. Search File Converter (files converted by user)
        try {
            // Assuming you have a FileConversion model
            if (class_exists(\App\Models\FileConversion::class)) {
                $conversions = \App\Models\FileConversion::where('user_id', Auth::id())
                    ->where('original_name', 'LIKE', "%{$query}%")
                    ->limit(3)
                    ->get();
                
                foreach ($conversions as $item) {
                    $results[] = [
                        'type' => 'conversion',
                        'title' => $item->original_name,
                        'subtitle' => 'Converted to ' . $item->output_format,
                        'url' => route('converter.history')
                    ];
                }
            }
        } catch (\Exception $e) {}
        
        return response()->json(['results' => $results]);
    }
}