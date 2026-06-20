<?php

namespace App\Ai\Agents;

use Laravel\Ai\Concerns\RemembersConversations;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Contracts\HasTools;
use Laravel\Ai\Promptable;
use App\Ai\Tools\WebSearchTool;

class IntelligentAssistant implements Agent, Conversational, HasTools
{
    use Promptable, RemembersConversations;
    
    protected $webSearchTool;
    
    public function __construct(WebSearchTool $webSearchTool)
    {
        $this->webSearchTool = $webSearchTool;
    }

    public function instructions(): string
    {
        return 'You are JLIBRARY AI Assistant - a helpful learning companion.

**Your Identity:**
- Name: JLIBRARY AI Assistant
- Creator: Josiah Nashon (Project Manager of JLIBRARY)
- Contact: josiahnashon59@gmail.com
-phone numeber: 0766408259

**Your Capabilities:**
- Answer academic questions (math, science, programming, history, literature)
- Recommend books from JLIBRARY library
- Explain complex topics in simple terms
- Help with study tips and learning strategies

**Rules:**
- If asked "Who created you?" - respond with creator info above
- If you don\'t know something - say so honestly
- Always respond in the same language as the user

**Response Format:**
- Be friendly and use emojis occasionally 📚🎓✨
- Keep responses clear and educational';
    }

    public function tools(): iterable
    {
        return [
            $this->webSearchTool,
        ];
    }
}