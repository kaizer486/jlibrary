<?php

namespace App\Http\Controllers;

use App\Services\GeminiNativeService;
use App\Models\ChatSession;
use App\Models\Document;
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
        
        // Get user's documents
        $documents = Document::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();
        
        // If document_id is in URL, pre-select it
        $selectedDocumentId = $request->document_id;
        
        // If there's a question in URL, auto-send it
        $autoQuestion = $request->question;

        // ==========================================
        // AUTO-ANALYZE: freshly uploaded document,
        // session has no messages yet, ?analyze=1.
        // Build the full analysis prompt server-side
        // so the JS on the page just fires it verbatim.
        // ==========================================
        $autoAnalyze = false;
        $autoAnalyzePrompt = null;
        $autoAnalyzeDisplay = null;

        $hasNoMessages = !$currentSession || empty($currentSession->messages) || count($currentSession->messages) === 0;

        if ($request->boolean('analyze') && $selectedDocumentId && $hasNoMessages) {
            $document = $documents->firstWhere('id', (int) $selectedDocumentId);

            if ($document) {
                $autoAnalyze = true;
                $autoAnalyzeDisplay = "📄 I've uploaded a document: \"{$document->title}\"";
                $autoAnalyzePrompt = "I've just uploaded a document titled \"{$document->title}\". Please analyze it and give me a comprehensive summary including:\n\n"
                    . "1. **Main topic/theme** - What is this document primarily about?\n"
                    . "2. **Key points** - What are the main arguments or findings?\n"
                    . "3. **Structure** - How is the document organized?\n"
                    . "4. **Key takeaways** - What are the most important things to remember?\n"
                    . "5. **Questions** - What questions should I ask about this document?\n\n"
                    . "Please provide a thorough analysis.";
            }
        }
        
        return view('ai.chat', compact(
            'sessions', 'currentSession', 'documents', 'selectedDocumentId', 'autoQuestion',
            'autoAnalyze', 'autoAnalyzePrompt', 'autoAnalyzeDisplay'
        ));
    }
    
    public function sendMessage(Request $request)
    {
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
        
        $request->validate([
            'message' => 'required|string|max:8000',
            'display_message' => 'nullable|string|max:2000',
            'session_id' => 'nullable|exists:chat_sessions,id',
            'document_id' => 'nullable|exists:documents,id'
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
            
            $userMessageToSave = $request->display_message ?: $request->message;

            // ==========================================
            // DOCUMENT CONTEXT
            // If a document_id was sent but the document has
            // no extracted text (PDF/DOCX parsing failed), tell
            // the user honestly instead of silently asking
            // Gemini to analyze a document it was never shown —
            // that produces confusing "I can't access files"
            // replies that look like a bug in the AI itself.
            // ==========================================
            $documentContext = null;

            if ($request->document_id) {
                $document = Document::where('id', $request->document_id)
                    ->where('user_id', Auth::id())
                    ->first();

                if ($document) {
                    $session->document_id = $document->id;
                    $session->save();

                    $extractedContent = trim($document->content ?? '');

                    Log::info('AI document context check', [
                        'document_id' => $document->id,
                        'content_length' => strlen($extractedContent),
                    ]);

                    if ($extractedContent === '') {
                        $session->addMessage('user', $userMessageToSave);

                        $warning = "I wasn't able to extract any readable text from \"{$document->title}\" "
                            . "(this usually happens with scanned/image-only PDFs, or if a required PDF/DOCX "
                            . "library isn't installed on the server). Could you try re-uploading it, or "
                            . "paste the relevant text directly here?";

                        $session->addMessage('assistant', $warning);

                        return response()->json([
                            'success' => true,
                            'response' => $warning,
                            'session_id' => $session->id,
                        ]);
                    }

                    $documentContext = [
                        'title' => $document->title,
                        'content' => substr($extractedContent, 0, 8000),
                    ];
                }
            }
            
            $messages = $session->getRecentMessages(20);
            
            $result = $this->gemini->chat(
                $request->message, 
                $messages,
                $documentContext
            );
            
            if (!$result['success']) {
                $statusCode = $result['status_code'] ?? 500;
                
                return response()->json([
                    'success' => false,
                    'response' => $result['response'],
                    'rate_limited' => $result['rate_limited'] ?? false,
                    'retry_after' => $result['retry_after'] ?? null
                ], $statusCode);
            }
            
            $session->addMessage('user', $userMessageToSave);
            $session->addMessage('assistant', $result['response']);
            
            return response()->json([
                'success' => true,
                'response' => $result['response'],
                'session_id' => $session->id
            ]);
            
        } catch (\Exception $e) {
            Log::error('AI Chat Error: ' . $e->getMessage());
            
            $errorMessage = $e->getMessage();
            
            if (strpos($errorMessage, '429') !== false || 
                strpos(strtolower($errorMessage), 'quota') !== false ||
                strpos(strtolower($errorMessage), 'rate limit') !== false) {
                
                $retrySeconds = 30;
                if (preg_match('/retry in (\d+\.?\d*)s/', $errorMessage, $matches)) {
                    $retrySeconds = ceil($matches[1]) + 5;
                }
                
                return response()->json([
                    'success' => false,
                    'response' => "The AI service has reached its free usage limit. Please wait about {$retrySeconds} seconds and try again.",
                    'rate_limited' => true,
                    'retry_after' => $retrySeconds
                ], 429);
            }
            
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
