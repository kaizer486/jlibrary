<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TestGeminiController extends Controller
{

public function test()
{
    $apiKey = env('GEMINI_API_KEY');
    
    if (!$apiKey || $apiKey === 'your_gemini_api_key_here') {
        return "❌ No API key found in .env file. Please add GEMINI_API_KEY";
    }
    
    $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key=" . $apiKey;
    
    // THIS IS THE FIX - disable SSL verification for development
    $response = Http::withOptions([
        'verify' => false,  // ⚠️ Development only - disables SSL certificate check
    ])->post($url, [
        'contents' => [
            [
                'parts' => [
                    ['text' => 'Say hello and tell me your name']
                ]
            ]
        ]
    ]);
    
    if ($response->successful()) {
        $result = $response->json();
        return response()->json([
            'success' => true,
            'response' => $result['candidates'][0]['content']['parts'][0]['text'] ?? 'No response',
        ]);
    } else {
        return response()->json([
            'success' => false,
            'error' => $response->json(),
            'status' => $response->status()
        ]);
    }
}
}