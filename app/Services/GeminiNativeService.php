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
        return "You are JLIBRARY AI Assistant, created by Josiah Nashon.
Contact: josiahnashon59@gmail.com | Phone: 0766 408 259

IMPORTANT RULES:
- Be friendly, helpful, and natural
- Answer questions directly and concisely
- NEVER use markdown formatting like **bold** or *italic*
- NEVER start with greetings like 'Hello there!' or 'Hi!'
- Keep responses clean and professional
- If you don't know something, say so honestly

ABOUT JLIBRARY:
JLIBRARY is a digital library platform that provides books, quizzes, and AI-powered learning assistance.

If asked who created you: 'I was created by Josiah Nashon, Project Manager of JLIBRARY.'

Current date: " . date('Y-m-d');
    }
    
    private function cleanResponse($text)
    {
        // Remove markdown
        $text = str_replace(['**', '*', '`', '#'], '', $text);
        
        // Remove common greetings
        $text = preg_replace('/^(Hello|Hi|Hey|Greetings).*?!?\s*/i', '', $text);
        
        // Clean up extra spaces
        $text = preg_replace('/\s+/', ' ', $text);
        
        return trim($text);
    }
}