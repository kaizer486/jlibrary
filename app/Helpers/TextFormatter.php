<?php

namespace App\Helpers;

class TextFormatter
{
    public static function format($text)
    {
        // Remove dividers and headers
        $text = str_replace('---', '', $text);
        $text = preg_replace('/\(Continued\)/i', '', $text);
        
        // Split into sentences
        $sentences = preg_split('/(?<=[.!?])\s+(?=[A-Z0-9])/', $text);
        
        $paragraphs = [];
        $currentParagraph = [];
        $count = 0;
        
        foreach ($sentences as $sentence) {
            $sentence = trim($sentence);
            if (empty($sentence)) continue;
            
            // Skip short headers
            if (strlen($sentence) < 15 && preg_match('/^[A-Z\s]+$/', $sentence)) {
                continue;
            }
            
            $currentParagraph[] = $sentence;
            $count++;
            
            if ($count % 2 == 0) {
                $paragraphs[] = implode(' ', $currentParagraph);
                $currentParagraph = [];
            }
        }
        
        if (!empty($currentParagraph)) {
            $paragraphs[] = implode(' ', $currentParagraph);
        }
        
        $html = '';
        foreach ($paragraphs as $paragraph) {
            if (trim($paragraph)) {
                $html .= '<p>' . htmlspecialchars(trim($paragraph)) . '</p>';
            }
        }
        
        return $html ?: '<p>' . htmlspecialchars($text) . '</p>';
    }
}