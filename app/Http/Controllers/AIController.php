<?php

namespace App\Http\Controllers;

use App\Services\GeminiNativeService;
use App\Models\ChatSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class AIController extends Controller
{
    protected $gemini;
    
    public function __construct(GeminiNativeService $gemini)
    {
        $this->gemini = $gemini;
    }
    
    public function index(Request $request)
    {
        $sessions = ChatSession::where('user_id', Auth::id())
            ->orderBy('updated_at', 'desc')
            ->get();
        
        $currentSession = null;
        
        if ($request->chat_session) {
            $currentSession = ChatSession::where('user_id', Auth::id())
                ->where('id', $request->chat_session)
                ->first();
        }
        
        if (!$currentSession && $sessions->isNotEmpty()) {
            $currentSession = $sessions->first();
        }
        
        return view('ai.chat', compact('sessions', 'currentSession'));
    }
    
    public function sendMessage(Request $request)
    {
        // RATE LIMIT CHECK - 10 requests per minute per user
        $userId = Auth::id();
        $cacheKey = "ai_rate_limit_{$userId}";
        $limit = 10;
        $timeWindow = 60;
        
        $requests = Cache::get($cacheKey, []);
        $now = time();
        
        $requests = array_filter($requests, function($timestamp) use ($now, $timeWindow) {
            return ($now - $timestamp) < $timeWindow;
        });
        
        if (count($requests) >= $limit) {
            $oldestRequest = min($requests);
            $waitTime = $timeWindow - ($now - $oldestRequest);
            
            return response()->json([
                'success' => false,
                'response' => "You're sending messages too quickly. Please wait {$waitTime} seconds before trying again.",
                'rate_limited' => true
            ], 429);
        }
        
        $requests[] = $now;
        Cache::put($cacheKey, $requests, $timeWindow);
        
        // Validate input
        $request->validate([
            'message' => 'required|string|max:2000',
            'session_id' => 'nullable|exists:chat_sessions,id'
        ]);
        
        try {
            $session = null;
            if ($request->session_id) {
                $session = ChatSession::where('user_id', Auth::id())
                    ->where('id', $request->session_id)
                    ->first();
            }
            
            if (!$session) {
                $session = ChatSession::create([
                    'user_id' => Auth::id(),
                    'title' => 'New Chat',
                    'messages' => []
                ]);
            }
            
            $messages = $session->getRecentMessages(20);
            
            $result = $this->gemini->chat($request->message, $messages);
            
            // Check if Gemini returned an error
            if (!$result['success']) {
                // Get status code from result or default to 500
                $statusCode = $result['status_code'] ?? 500;
                
                return response()->json([
                    'success' => false,
                    'response' => $result['response'],
                    'rate_limited' => $result['rate_limited'] ?? false,
                    'retry_after' => $result['retry_after'] ?? null
                ], $statusCode);
            }
            
            // Add messages using the model's helper method
            $session->addMessage('user', $request->message);
            $session->addMessage('assistant', $result['response']);
            
            return response()->json([
                'success' => true,
                'response' => $result['response'],
                'session_id' => $session->id
            ]);
            
        } catch (\Exception $e) {
            Log::error('AI Chat Error: ' . $e->getMessage());
            
            $errorMessage = $e->getMessage();
            
            // Check if it's a rate limit/quota error
            if (strpos($errorMessage, '429') !== false || 
                strpos(strtolower($errorMessage), 'quota') !== false ||
                strpos(strtolower($errorMessage), 'rate limit') !== false) {
                
                $retrySeconds = 30;
                if (preg_match('/retry in (\d+\.?\d*)s/', $errorMessage, $matches)) {
                    $retrySeconds = ceil($matches[1]) + 5;
                }
                
                return response()->json([
                    'success' => false,
                    'response' => "The AI service has reached its free usage limit. Please wait about {$retrySeconds} seconds and try again. You can upgrade to a paid plan for higher limits.",
                    'rate_limited' => true,
                    'retry_after' => $retrySeconds
                ], 429);
            }
            
            // Generic error
            return response()->json([
                'success' => false,
                'response' => 'Sorry, something went wrong. Please try again.',
                'error' => config('app.debug') ? $errorMessage : null
            ], 500);
        }
    }
    
    public function newSession()
    {
        $session = ChatSession::create([
            'user_id' => Auth::id(),
            'title' => 'New Chat',
            'messages' => []
        ]);
        
        return response()->json([
            'success' => true,
            'session_id' => $session->id
        ]);
    }
    
    public function deleteSession($id)
    {
        $session = ChatSession::where('user_id', Auth::id())
            ->where('id', $id)
            ->first();
        
        if ($session) {
            $session->delete();
            return response()->json(['success' => true]);
        }
        
        return response()->json(['success' => false], 404);
    }
    
    public function getSession($id)
    {
        $session = ChatSession::where('user_id', Auth::id())
            ->where('id', $id)
            ->first();
        
        if ($session) {
            return response()->json([
                'success' => true,
                'messages' => $session->messages ?? []
            ]);
        }
        
        return response()->json(['success' => false], 404);
    }
}