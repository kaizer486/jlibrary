<?php

namespace App\Http\Controllers;

use App\Services\GeminiService;
use App\Models\ChatSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AIController extends Controller
{
    protected $gemini;
    
    public function __construct(GeminiService $gemini)
    {
        $this->gemini = $gemini;
    }
    
  public function index(Request $request)
{
    // Get all sessions for sidebar
    $sessions = ChatSession::where('user_id', Auth::id())
        ->orderBy('updated_at', 'desc')
        ->get();
    
    $currentSession = null;
    
    if ($request->chat_session) {
        $currentSession = ChatSession::where('user_id', Auth::id())
            ->where('id', $request->chat_session)
            ->first();
    }
    
    // If no current session but has sessions, get the first one
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
        
        // Get or create session
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
        
        // Get AI response
        $result = $this->gemini->chat($request->message);
        
        if ($result['success']) {
            // Save messages
            $messages = $session->messages ?? [];
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
            
            $session->update([
                'messages' => $messages,
                'updated_at' => now()
            ]);
        }
        
        return response()->json([
            'success' => $result['success'],
            'response' => $result['response'],
            'session_id' => $session->id
        ]);
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