<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    protected $apiKey;
    protected $model;
    
    public function __construct()
    {
      $this->apiKey = config('services.gemini.api_key');
      $this->model = config('services.gemini.model', 'gemini-2.5-flash');
    }
    
    public function chat($message)
    {
        try {
            $systemPrompt = "You are JLIBRARY AI Assistant, created by Josiah Nashon.
Contact: josiahnashon59@gmail.com | Phone: 0766 408 259

IMPORTANT:
- Be friendly and natural
- If someone greets you with 'Hi', 'Hello', 'Hey', respond warmly
- Answer questions directly and helpfully
- Keep responses conversational but professional
- No markdown or **stars** formatting


If asked who created you, say: 'I was created by Josiah Nashon, Project Manager of JLIBRARY. Contact: josiahnashon59@gmail.com | Phone: 0766 408 259'

Example of correct response:
'The capital of France is Paris. It is known for the Eiffel Tower.'

Example of WRONG response (NEVER do this):
'Hello there! The capital is **Paris**. 😊'";

            $response = Http::timeout(30)
                ->withOptions(['verify' => false])
                ->post("https://generativelanguage.googleapis.com/v1/models/{$this->model}:generateContent?key={$this->apiKey}", [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $systemPrompt . "\n\nUser: " . $message]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'temperature' => 0.5,
                        'maxOutputTokens' => 500,
                    ]
                ]);
            
            if ($response->successful()) {
                $text = $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? '';
                
                // Clean up formatting
                $text = str_replace('**', '', $text);
                $text = str_replace('*', '', $text);
                $text = preg_replace('/^Hello there!?\s*/i', '', $text);
                $text = preg_replace('/^Hi there!?\s*/i', '', $text);
                $text = preg_replace('/^Hey!?\s*/i', '', $text);
                $text = preg_replace('/^Greetings!?\s*/i', '', $text);
                
                return [
                    'success' => true,
                    'response' => trim($text)
                ];
            }
            
            return [
                'success' => false,
                'response' => 'Sorry, I could not process that request. Please try again.'
            ];
            
        } catch (\Exception $e) {
            Log::error('Gemini API Error: ' . $e->getMessage());
            return [
                'success' => false,
                'response' => 'Connection error. Please check your internet and try again.'
            ];
        }
    }
}