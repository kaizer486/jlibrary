<?php

namespace App\Http\Controllers;

use App\Services\GeminiNativeService;
use App\Models\ChatSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AIController extends Controller
{
    protected $gemini;
    
    public function __construct(GeminiNativeService $gemini)
    {
        $this->gemini = $gemini;
    }
    
    private function formatResponse($text)
{
    // Split into sentences
    $sentences = preg_split('/(?<=[.!?])\s+(?=[A-Z])/', $text);
    
    $result = "";
    $count = 0;
    
    foreach ($sentences as $sentence) {
        $sentence = trim($sentence);
        if (empty($sentence)) continue;
        
        $count++;
        
        // Add line break every 2-3 sentences
        if ($count % 3 == 0) {
            $result .= $sentence . "\n\n";
        } else {
            $result .= $sentence . " ";
        }
    }
    
    // Add line breaks before bullet points
    $result = preg_replace('/([.!?])\s+([•\-])/', "$1\n$2", $result);
    
    // Add line breaks before numbers
    $result = preg_replace('/([.!?])\s+(\d+\.)/', "$1\n$2", $result);
    
    // Ensure --- has breaks
    $result = preg_replace('/---/', "\n\n---\n\n", $result);
    
    return trim($result);
}
    // ✅ ADD THIS METHOD IF MISSING
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
                    'title' => substr($request->message, 0, 50),
                    'messages' => []
                ]);
            }
            
            $messages = $session->messages ?? [];
            
            $result = $this->gemini->chat($request->message, $messages);
            
            $messages[] = [
                'role' => 'user',
                'content' => $request->message,
                'timestamp' => now()->toIso8601String()
            ];
            $messages[] = [
                'role' => 'assistant',
                'content' => $result['response'],
                'timestamp' => now()->toIso8601String()
            ];
            
            if (count($messages) <= 2) {
                $title = substr($request->message, 0, 40) . (strlen($request->message) > 40 ? '...' : '');
                $session->title = $title;
            }
            
            $session->update([
                'messages' => $messages,
                'updated_at' => now()
            ]);
            
            return response()->json([
                'success' => true,
                'response' => $result['response'],
                'session_id' => $session->id
            ]);
            
        } catch (\Exception $e) {
            Log::error('AI Chat Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'response' => 'Sorry, something went wrong. Please try again.'
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