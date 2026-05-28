<?php

return [
    'default' => env('AI_PROVIDER', 'gemini'),
    
    'providers' => [
        'gemini' => [
            'driver' => 'gemini',
            'api_key' => env('GEMINI_API_KEY'),
            'model' => env('GEMINI_MODEL', 'gemini-2.0-flash-exp'),
            'base_url' => 'https://generativelanguage.googleapis.com/v1beta',
            'with_web_search' => true,  // Enable Google Search grounding!
        ],
    ],
    
    'features' => [
        'web_search' => env('AI_WEB_SEARCH', true),  // Allow web search
        'file_search' => env('AI_FILE_SEARCH', true),  // Allow file search
        'memory' => env('AI_MEMORY', true),  // Remember conversations
    ],
    
    'agent' => \App\Ai\Agents\IntelligentAssistant::class,
];