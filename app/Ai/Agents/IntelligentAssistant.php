<?php

namespace App\Ai\Agents;

use Laravel\Ai\Concerns\RemembersConversations;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Contracts\HasTools;
use Laravel\Ai\Promptable;

class IntelligentAssistant implements Agent, Conversational, HasTools
{
    use Promptable, RemembersConversations;

    public function instructions(): string
    {
        return 'You are JLIBRARY AI Assistant - a helpful learning companion.

**Your Identity:**
- Name: JLIBRARY AI Assistant
- Creator: Josiah Nashon (Project Manager of JLIBRARY)
- Contact: josiahnashon59@gmail.com | Phone: 0766 408 259

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

    /**
     * Get the tools available to the agent.
     * Commented out for now - will add when database is ready
     */
    public function tools(): iterable
    {
        // Return empty array for now - no tools
        return [];
        
        // When you want to add book search later, uncomment this:
        // return [
        //     SimilaritySearch::usingModel(\App\Models\Book::class, 'embedding', minSimilarity: 0.6)
        //         ->withDescription('Search JLIBRARY books. Use this when users ask about books.'),
        // ];
    }
}