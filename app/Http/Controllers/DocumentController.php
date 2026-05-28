<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    // Show all user documents
    public function index()
    {
        $documents = Document::where('user_id', Auth::id())->latest()->get();
        return view('documents.index', compact('documents'));
    }
    
    // Show upload form
    public function create()
    {
        return view('documents.upload');
    }
    
    // Upload and process document
    public function upload(Request $request)
    {
        $request->validate([
            'document' => 'required|file|mimes:pdf,txt,docx|max:10240', // Max 10MB
            'title' => 'required|string|max:255'
        ]);
        
        $file = $request->file('document');
        $originalName = $file->getClientOriginalName();
        $fileType = $file->getClientMimeType();
        $fileSize = $file->getSize();
        
        // Store the file
        $path = $file->store('documents', 'public');
        
        // Extract text content from document
        $content = $this->extractTextFromFile($file);
        
        // Create document record
        $document = Document::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'content' => $content,
            'file_path' => $path,
            'file_type' => $fileType,
            'file_size' => $fileSize
        ]);
        
        return redirect()->route('documents.index')
                         ->with('success', 'Document uploaded and processed successfully!');
    }
    
    // Extract text from various file types using Gemini API
    private function extractTextFromFile($file)
    {
        $extension = $file->getClientOriginalExtension();
        $apiKey = env('GEMINI_API_KEY');
        
        // For text files, just read directly
        if ($extension === 'txt') {
            return file_get_contents($file->getPathname());
        }
        
        // For PDF and DOCX, use Gemini API to extract text
        if (($extension === 'pdf' || $extension === 'docx') && $apiKey) {
            try {
                $fileContent = base64_encode(file_get_contents($file->getPathname()));
                $mimeType = $extension === 'pdf' ? 'application/pdf' : 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
                
                $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=" . $apiKey;
                
                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                ])->post($url, [
                    'contents' => [
                        [
                            'parts' => [
                                [
                                    'inline_data' => [
                                        'mime_type' => $mimeType,
                                        'data' => $fileContent
                                    ]
                                ],
                                [
                                    'text' => 'Extract and return ALL the text content from this document. Preserve paragraphs and structure. Return only the raw text, no additional commentary.'
                                ]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'temperature' => 0.1,
                        'maxOutputTokens' => 8192,
                    ]
                ]);
                
                if ($response->successful()) {
                    $result = $response->json();
                    return $result['candidates'][0]['content']['parts'][0]['text'] ?? 'Could not extract text.';
                }
            } catch (\Exception $e) {
                return "Error extracting text: " . $e->getMessage();
            }
        }
        
        return "Text extraction not available for this file type.";
    }
    
    // View a single document
    public function show(Document $document)
    {
        // Check ownership
        if ($document->user_id !== Auth::id()) {
            abort(403);
        }
        
        return view('documents.show', compact('document'));
    }
    
    // Ask questions about a document
    public function ask(Request $request, Document $document)
    {
        // Check ownership
        if ($document->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $request->validate([
            'question' => 'required|string|max:2000'
        ]);
        
        $apiKey = env('GEMINI_API_KEY');
        
        if (!$apiKey) {
            return response()->json([
                'answer' => 'Please add your GEMINI_API_KEY to .env file to analyze documents.'
            ]);
        }
        
        try {
            $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=" . $apiKey;
            
            // Use only first 30000 characters to stay within token limits
            $contentPreview = substr($document->content, 0, 30000);
            
            $prompt = "Document Title: {$document->title}\n\n";
            $prompt .= "Document Content:\n{$contentPreview}\n\n";
            $prompt .= "User Question: {$request->question}\n\n";
            $prompt .= "Instructions: Answer the user's question based ONLY on the document content above. ";
            $prompt .= "If the answer is not found in the document, say 'I could not find that information in this document.' ";
            $prompt .= "Be specific and quote relevant parts when possible.";
            
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($url, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.3,
                    'maxOutputTokens' => 2000,
                ]
            ]);
            
            if ($response->successful()) {
                $result = $response->json();
                $answer = $result['candidates'][0]['content']['parts'][0]['text'] ?? 'Unable to generate answer.';
                
                return response()->json(['answer' => $answer]);
            }
            
            return response()->json(['answer' => 'Error processing your question. Please try again.']);
            
        } catch (\Exception $e) {
            return response()->json(['answer' => 'Error: ' . $e->getMessage()]);
        }
    }
    
    // Delete a document
    public function destroy(Document $document)
    {
        // Check ownership
        if ($document->user_id !== Auth::id()) {
            abort(403);
        }
        
        // Delete file from storage
        if ($document->file_path) {
            Storage::disk('public')->delete($document->file_path);
        }
        
        $document->delete();
        
        return redirect()->route('documents.index')
                         ->with('success', 'Document deleted successfully.');
    }
}