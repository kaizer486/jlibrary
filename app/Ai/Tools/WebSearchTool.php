<?php

namespace App\Ai\Tools;

use App\Services\WebSearchService;

class WebSearchTool
{
    protected $webSearch;
    
    public function __construct(WebSearchService $webSearch)
    {
        $this->webSearch = $webSearch;
    }
    
    public function name(): string
    {
        return 'web_search';
    }
    
    public function description(): string
    {
        return 'Search the web for live information like news, weather, current events, and recent updates.';
    }
    
    public function execute(string $query): string
    {
        $results = $this->webSearch->search($query);
        
        if (!$results) {
            return 'Could not fetch search results.';
        }
        
        return $results;
    }
}