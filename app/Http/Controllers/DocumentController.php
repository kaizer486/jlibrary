<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\ChatSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DocumentController extends Controller
{
    public function index()
    {
        $documents = Document::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(12);
        
        return view('documents.index', compact('documents'));
    }

    public function create()
    {
        return view('documents.create');
    }

    public function upload(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'document' => 'required|file|mimes:pdf,txt,docx|max:10240',
        ]);

        try {
            $file = $request->file('document');
            $originalName = $file->getClientOriginalName();
            $fileSize = $file->getSize();
            $fileType = $file->getClientMimeType();
            
            $filename = time() . '_' . Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('documents/' . Auth::id(), $filename, 'public');
            
            // Extract text content
            $content = $this->extractTextFromFile($file, $fileType);

            Log::info('Document text extraction result', [
                'title' => $request->title,
                'extension' => $file->getClientOriginalExtension(),
                'extracted_length' => strlen($content),
            ]);
            
            // Truncate if needed (LONGTEXT handles it, but safe)
            if (strlen($content) > 100000) {
                $content = substr($content, 0, 100000) . "\n\n... [Content truncated for processing] ...\n\n";
            }
            
            $document = Document::create([
                'user_id' => Auth::id(),
                'title' => $request->title,
                'content' => $content,
                'file_path' => $path,
                'file_type' => $fileType,
                'file_size' => $fileSize,
            ]);
            
            $session = ChatSession::create([
                'user_id' => Auth::id(),
                'title' => '📄 ' . $request->title,
                'messages' => []
            ]);

            $session->document_id = $document->id;
            $session->save();
            
            return redirect()->route('ai.chat', [
                'chat_session' => $session->id,
                'document_id' => $document->id,
                'analyze' => 1, // tells the AI page to auto-run the analysis prompt
            ])->with('success', 'Document uploaded! Analyzing it now...');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to upload document: ' . $e->getMessage());
        }
    }

    public function show(Document $document)
    {
        if ($document->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this document.');
        }
        
        return view('documents.show', compact('document'));
    }

    /**
     * "Chat with Doc" button target.
     * Reuses the most recent session already tied to this document if one
     * exists and has messages (so clicking it again just re-opens that
     * conversation instead of re-running analysis every time). Otherwise
     * creates a fresh session and sends the user to the AI page with
     * analyze=1, exactly like a brand-new upload does.
     */
    public function chat(Document $document)
    {
        if ($document->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this document.');
        }

        $existingSession = ChatSession::where('user_id', Auth::id())
            ->where('document_id', $document->id)
            ->orderBy('updated_at', 'desc')
            ->first();

        if ($existingSession && !empty($existingSession->messages)) {
            return redirect()->route('ai.chat', [
                'chat_session' => $existingSession->id,
                'document_id' => $document->id,
            ]);
        }

        $session = ChatSession::create([
            'user_id' => Auth::id(),
            'title' => '📄 ' . $document->title,
            'messages' => []
        ]);

        $session->document_id = $document->id;
        $session->save();

        return redirect()->route('ai.chat', [
            'chat_session' => $session->id,
            'document_id' => $document->id,
            'analyze' => 1,
        ]);
    }

    public function ask(Request $request, Document $document)
    {
        $request->validate([
            'question' => 'required|string|min:3|max:1000',
        ]);
        
        if ($document->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $session = ChatSession::create([
            'user_id' => Auth::id(),
            'title' => '📄 ' . $document->title . ' - Q&A',
            'messages' => []
        ]);
        
        $session->document_id = $document->id;
        $session->save();
        
        return redirect()->route('ai.chat', [
            'chat_session' => $session->id,
            'document_id' => $document->id,
            'question' => $request->question,
        ]);
    }

    public function destroy(Document $document)
    {
        if ($document->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }
        
        if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }
        
        $document->delete();
        
        return redirect()->route('documents.index')
            ->with('success', 'Document deleted successfully.');
    }

    private function extractTextFromFile($file, $fileType)
    {
        $extension = $file->getClientOriginalExtension();
        $path = $file->getRealPath();
        
        try {
            switch (strtolower($extension)) {
                case 'txt':
                    return file_get_contents($path);
                    
                case 'pdf':
                    return $this->extractTextFromPDF($path);
                    
                case 'docx':
                    return $this->extractTextFromDOCX($path);
                    
                default:
                    return '';
            }
        } catch (\Throwable $e) {
            Log::error('extractTextFromFile failed', [
                'extension' => $extension,
                'error' => $e->getMessage(),
            ]);
            return '';
        }
    }

    private function extractTextFromPDF($path)
    {
        Log::info('extractTextFromPDF starting', [
            'path' => $path,
            'exists' => file_exists($path),
            'size' => file_exists($path) ? filesize($path) : null,
            'smalot_available' => class_exists('Smalot\PdfParser\Parser'),
        ]);

        if (class_exists('Smalot\PdfParser\Parser')) {
            try {
                $parser = new \Smalot\PdfParser\Parser();
                $pdf = $parser->parseFile($path);
                $text = $pdf->getText();

                Log::info('Smalot PDF parse succeeded', [
                    'extracted_length' => strlen($text),
                ]);

                if (trim($text) !== '') {
                    return $text;
                }
                Log::warning('Smalot parsed the PDF but extracted zero characters (likely scanned/image-only PDF)');
            } catch (\Throwable $e) {
                Log::error('Smalot PDF parse threw an exception', [
                    'error' => $e->getMessage(),
                    'class' => get_class($e),
                ]);
            }
        } else {
            Log::warning('Smalot\PdfParser\Parser class not found — composer package not installed or autoload not refreshed');
        }
        
        if (function_exists('shell_exec')) {
            try {
                $escapedPath = escapeshellarg($path);
                $output = shell_exec("pdftotext -layout {$escapedPath} - 2>&1");

                Log::info('pdftotext fallback attempted', [
                    'output_length' => $output ? strlen($output) : 0,
                ]);

                if ($output && trim($output) !== '') {
                    return $output;
                }
            } catch (\Throwable $e) {
                Log::error('pdftotext fallback failed', ['error' => $e->getMessage()]);
            }
        } else {
            Log::warning('shell_exec is disabled on this server — pdftotext fallback unavailable');
        }
        
        return '';
    }

    private function extractTextFromDOCX($path)
    {
        if (class_exists('PhpOffice\PhpWord\PhpWord')) {
            try {
                $phpWord = \PhpOffice\PhpWord\IOFactory::load($path);
                $text = '';
                foreach ($phpWord->getSections() as $section) {
                    foreach ($section->getElements() as $element) {
                        if (method_exists($element, 'getText')) {
                            $text .= $element->getText() . "\n";
                        }
                    }
                }
                return $text;
            } catch (\Throwable $e) {
                Log::error('PhpWord DOCX parse failed', ['error' => $e->getMessage()]);
                return '';
            }
        }
        
        try {
            $zip = new \ZipArchive();
            if ($zip->open($path) === true) {
                $document = $zip->getFromName('word/document.xml');
                $zip->close();
                return strip_tags($document ?? '');
            }
        } catch (\Throwable $e) {
            Log::error('ZipArchive DOCX fallback failed', ['error' => $e->getMessage()]);
            return '';
        }
        
        return '';
    }
}
