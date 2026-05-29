<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WebSearchService
{
    public function search($query)
    {
        try {
            // Free DuckDuckGo search - no API key needed
            $response = Http::timeout(10)->get('https://api.duckduckgo.com/', [
                'q' => $query,
                'format' => 'json',
                'no_html' => 1,
                'skip_disambig' => 1
            ]);
            
            $data = $response->json();
            $results = [];
            
            if (!empty($data['AbstractText'])) {
                $results[] = $data['AbstractText'];
            }
            
            if (!empty($data['RelatedTopics'])) {
                foreach ($data['RelatedTopics'] as $topic) {
                    if (isset($topic['Text']) && count($results) < 3) {
                        $results[] = $topic['Text'];
                    }
                }
            }
            
            return !empty($results) ? implode("\n\n", $results) : null;
            
        } catch (\Exception $e) {
            Log::error('Web search error: ' . $e->getMessage());
            return null;
        }
    }
    
    public function needsSearch($message)
    {
        $keywords = ['today', 'now', 'latest', 'current', 'news', 'weather', 'price', 'stock', 'score', 'update', 'recent'];
        $message = strtolower($message);
        
        foreach ($keywords as $keyword) {
            if (strpos($message, $keyword) !== false) {
                return true;
            }
        }
        return false;
    }
}