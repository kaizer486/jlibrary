<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiNativeService
{
    protected $apiKey;
    protected $model;
    protected $baseUrl;
    protected $timeout;
    
    public function __construct()
    {
        $this->apiKey = env('GEMINI_API_KEY');
        $this->model = env('GEMINI_MODEL', 'gemini-2.5-flash');
        $this->baseUrl = 'https://generativelanguage.googleapis.com/v1beta'; // Use v1beta
        $this->timeout = 60;
    }
    
    public function chat($message, $previousMessages = [])
    {
        try {
            $contents = [];
            
            // Add conversation history
            foreach ($previousMessages as $msg) {
                // Skip system messages or errors
                if ($msg['role'] === 'assistant' && strpos($msg['content'], 'error') !== false) {
                    continue;
                }
                $contents[] = [
                    'role' => $msg['role'] === 'user' ? 'user' : 'model',
                    'parts' => [['text' => $msg['content']]]
                ];
            }
            
            // Add current message
            $contents[] = [
                'role' => 'user',
                'parts' => [['text' => $message]]
            ];
            
            // Build request with system instruction (v1beta format)
            $requestBody = [
                'contents' => $contents,
                'system_instruction' => [
                    'parts' => [['text' => $this->getSystemPrompt()]]
                ],
                'generationConfig' => [
                    'temperature' => 0.7,
                    'maxOutputTokens' => 800,
                    'topP' => 0.95,
                ]
            ];
            
            Log::info('Sending to Gemini', ['model' => $this->model, 'message' => substr($message, 0, 100)]);
            
            $response = Http::timeout($this->timeout)
                ->withOptions(['verify' => false])
                ->post("{$this->baseUrl}/models/{$this->model}:generateContent?key={$this->apiKey}", $requestBody);
            
            if ($response->successful()) {
                $data = $response->json();
                $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? 'No response';
                $text = $this->cleanResponse($text);
                
                return [
                    'success' => true,
                    'response' => trim($text),
                    'search_used' => false
                ];
            }
            
            // Log error for debugging
            $errorBody = $response->json();
            $errorMessage = $errorBody['error']['message'] ?? $response->body();
            Log::error('Gemini API Error', [
                'status' => $response->status(),
                'message' => $errorMessage,
                'model' => $this->model
            ]);
            
            // Return friendly error
            return [
                'success' => false,
                'response' => 'I apologize, but I encountered an error. Please try again.',
                'search_used' => false
            ];
            
        } catch (\Exception $e) {
            Log::error('Gemini Exception: ' . $e->getMessage());
            return [
                'success' => false,
                'response' => 'I apologize, but I encountered an error. Please try again.',
                'search_used' => false
            ];
        }
    }
    
  private function getSystemPrompt()
{
    return "You are JLIBRARY AI Assistant.

RESPONSE FORMAT (MUST FOLLOW STRICTLY):

When answering questions, ALWAYS use this exact structure:

Main Topic Title

One short introductory paragraph (2-3 sentences).
---
1. First Point Title

2-3 sentences explaining this point.

**Key points:**
• First bullet point
• Second bullet point
• Third bullet point

> Optional insight or quote

---

### 2. Second Point Title

2-3 sentences explaining this point.

**Key elements:**
• First bullet
• Second bullet

---

3. Third Point Title

2-3 sentences explaining this point.

**Main takeaways:**
• Bullet one
• Bullet two

---

**Would you like me to explain any part further?**

RULES:
- Use ## for main title
- Use ### for numbered points (1., 2., 3.)
- Use **bold** for subheadings like 'Key points:'
- Use • for bullet points
- Use > for important quotes or insights
- Use --- as divider between sections
- Always end with a question
- NEVER write long dense paragraphs
- Put blank lines between every section

Current date: " . date('Y-m-d');
} 
private function cleanResponse($text)
{
    // Only remove markdown symbols that break formatting
    $text = str_replace(['**', '`'], '', $text);
    
    // Keep # for headings, keep • for bullets, keep > for quotes
    // Do NOT remove these
    
    return trim($text);
}
}