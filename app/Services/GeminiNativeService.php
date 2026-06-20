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
        $this->model = env('GEMINI_MODEL', 'gemini-2.0-flash-lite');
        $this->baseUrl = 'https://generativelanguage.googleapis.com/v1beta';
        $this->timeout = 120;
    }
    
    public function chat($message, $previousMessages = [])
    {
        try {
            $contents = [];
            
            foreach ($previousMessages as $msg) {
                if ($msg['role'] === 'assistant' && strpos($msg['content'], 'error') !== false) {
                    continue;
                }
                $contents[] = [
                    'role' => $msg['role'] === 'user' ? 'user' : 'model',
                    'parts' => [['text' => $msg['content']]]
                ];
            }
            
            $contents[] = [
                'role' => 'user',
                'parts' => [['text' => $message]]
            ];
            
            $requestBody = [
                'contents' => $contents,
                'system_instruction' => [
                    'parts' => [['text' => $this->getSystemPrompt()]]
                ],
                'generationConfig' => [
                    'temperature' => 0.8,
                    'maxOutputTokens' => 2048,
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
            
            $errorBody = $response->json();
            $errorMessage = $errorBody['error']['message'] ?? $response->body();
            $statusCode = $response->status();
            
            Log::error('Gemini API Error', [
                'status' => $statusCode,
                'message' => $errorMessage,
                'model' => $this->model
            ]);
            
            // Handle 429 - Rate Limit
            if ($statusCode == 429) {
                $retrySeconds = 30;
                if (preg_match('/retry in (\d+\.?\d*)s/', $errorMessage, $matches)) {
                    $retrySeconds = ceil($matches[1]) + 5;
                }
                
                return [
                    'success' => false,
                    'response' => "The AI service has reached its free usage limit. Please wait about {$retrySeconds} seconds and try again.",
                    'status_code' => 429,
                    'rate_limited' => true,
                    'retry_after' => $retrySeconds
                ];
            }
            
            // Handle 503 - Service Unavailable (High Demand)
            if ($statusCode == 503) {
                return [
                    'success' => false,
                    'response' => 'The AI service is currently experiencing high demand. Please wait a moment and try again. If the issue persists, try asking a simpler question.',
                    'status_code' => 503,
                    'rate_limited' => false,
                    'retry_after' => 10
                ];
            }
            
            if ($statusCode == 404) {
                return [
                    'success' => false,
                    'response' => 'The AI model is not available. Please contact support.',
                    'status_code' => 404,
                    'rate_limited' => false
                ];
            }
            
            return [
                'success' => false,
                'response' => 'I encountered an error processing your request. Please try again.',
                'status_code' => $statusCode,
                'rate_limited' => false
            ];
            
        } catch (\Exception $e) {
            Log::error('Gemini Exception: ' . $e->getMessage());
            
            $errorMessage = $e->getMessage();
            
            if (strpos($errorMessage, 'timed out') !== false || 
                strpos($errorMessage, 'cURL error 28') !== false) {
                return [
                    'success' => false,
                    'response' => 'The AI service is taking too long to respond. Please try again in a moment.',
                    'status_code' => 504,
                    'rate_limited' => false
                ];
            }
            
            return [
                'success' => false,
                'response' => 'I encountered an error processing your request. Please try again.',
                'status_code' => 500,
                'rate_limited' => false
            ];
        }
    }
    
    private function getSystemPrompt(): string
    {
        $currentDate = date('D, d M Y');

        return <<<PROMPT
You are JLIBRARY AI Assistant - a knowledgeable, friendly academic librarian.

ABOUT YOU:
- You speak naturally like a helpful librarian
- You answer directly - no filler phrases
- You match the user's language and tone
- You give comprehensive, detailed answers
- You NEVER give one-sentence answers to "explain" questions

ABOUT JLIBRARY:
- JLIBRARY is a digital library platform
- Features: library, quizzes, certificates, wallet, community, marketplace
- Your creator: Josiah Nashon (Project Manager)
- Contact: josiahnashon59@gmail.com

CRITICAL FORMATTING RULES:

1. FOR LISTS WITH MAIN POINTS:
   Use this EXACT structure:
   
   [Introductory sentence]
   
   1. **Main Point Title**
   
   [2-3 sentences explaining this point in detail.]
   
   • Sub-point one
   • Sub-point two
   • Sub-point three
   
   2. **Second Main Point Title**
   
   [2-3 sentences explaining this point in detail.]
   
   • Sub-point one
   • Sub-point two

2. RULES:
   - MUST include numbers: 1., 2., 3.
   - Number AND bold title on SAME line: 1. **Title**
   - Explanation on NEW LINE below title
   - Bullet points on their OWN lines
   - Blank line between each main point
   - End with a follow-up question

3. NEVER use:
   - Hash symbols (#, ##, ###)
   - Dashes for main points
   - Explanation on same line as title

EXAMPLE:
User: "explain why coding is important"

You:
"Coding has become an indispensable skill in the modern world. Here are three key reasons why coding is so important:

1. **Problem-Solving and Critical Thinking**

Learning to code trains individuals to approach problems systematically.

• Develops logical reasoning
• Encourages systematic thinking

2. **Career Opportunities and Demand**

Coding is at the heart of numerous high-growth careers.

• High demand in tech industry
• Diverse job roles available

3. **Innovation and Digital Creation**

Coding empowers individuals to create new technologies.

• Enables development of new software
• Powers digital services

Would you like to know more about how coding impacts specific industries?"

Current date: {$currentDate}
PROMPT;
    }
    
    private function cleanResponse($text)
    {
        // Remove filler phrases
        $text = preg_replace('/^(Great question!|That\'s a fantastic question!|Wonderful question!|Excellent question!|That\'s a great way to)/i', '', $text);
        
        // Remove markdown
        $text = str_replace(['**', '*', '`', '_', '~'], '', $text);
        $text = preg_replace('/^#{1,6}\s+/m', '', $text);
        $text = preg_replace('/\[([^\]]+)\]\([^\)]+\)/', '$1', $text);
        $text = preg_replace('/^>\s+/m', '', $text);
        $text = preg_replace('/^[\-*_]{3,}$/m', '', $text);
        $text = preg_replace('/\n{3,}/', "\n\n", $text);
        $text = preg_replace('/^[\s]*[-*+]\s+/m', '• ', $text);
        
        return trim($text);
    }
}