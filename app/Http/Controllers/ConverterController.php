<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Settings;
use Smalot\PdfParser\Parser;
use setasign\Fpdi\Fpdi;
use Exception;

class ConverterController extends Controller
{
    /**
     * Display the converter page
     */
    public function index()
    {
        return view('converter.index');
    }

    /**
     * Convert PDF to Word
     */
    public function pdfToWord(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf|max:10240' // 10MB max
        ]);

        try {
            $file = $request->file('file');
            $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            
            // Store the uploaded PDF
            $pdfPath = $file->storeAs('temp', $file->hashName(), 'public');
            $fullPdfPath = Storage::disk('public')->path($pdfPath);
            
            // Create a new Word document
            $phpWord = new \PhpOffice\PhpWord\PhpWord();
            $section = $phpWord->addSection();
            
            // Extract text from PDF using FPDI
            $pdf = new Fpdi();
            $pageCount = $pdf->setSourceFile($fullPdfPath);
            
            for ($i = 1; $i <= $pageCount; $i++) {
                $tplId = $pdf->importPage($i);
                $size = $pdf->getTemplateSize($tplId);
                
                // Add page to Word document
                $section->addText("Page " . $i . " of " . $pageCount, ['bold' => true, 'size' => 16]);
                $section->addTextBreak(1);
                
                // Extract text content (simplified - for better results use a PDF text extractor)
                $text = $this->extractTextFromPdf($fullPdfPath, $i);
                $section->addText($text ?? 'No text extracted from this page.');
                $section->addTextBreak(2);
            }
            
            // Save Word document
            $wordFilename = $filename . '_converted.docx';
            $wordPath = 'temp/' . uniqid() . '.docx';
            $fullWordPath = Storage::disk('public')->path($wordPath);
            
            $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
            $objWriter->save($fullWordPath);
            
            // Clean up PDF
            Storage::disk('public')->delete($pdfPath);
            
            return response()->download($fullWordPath, $wordFilename)->deleteFileAfterSend(true);
            
        } catch (Exception $e) {
            Log::error('PDF to Word conversion error: ' . $e->getMessage());
            return back()->with('error', 'Conversion failed: ' . $e->getMessage());
        }
    }

    /**
     * Convert Word to PDF
     */
    public function wordToPdf(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:doc,docx|max:10240'
        ]);

        try {
            $file = $request->file('file');
            $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            
            // Store the uploaded Word file
            $wordPath = $file->storeAs('temp', $file->hashName(), 'public');
            $fullWordPath = Storage::disk('public')->path($wordPath);
            
            // Load Word document
            $phpWord = IOFactory::load($fullWordPath);
            
            // Create PDF using FPDF/FPDI
            $pdf = new Fpdi();
            $pdf->AddPage();
            $pdf->SetFont('Arial', '', 12);
            
            // Extract text from Word and add to PDF
            $section = $phpWord->getSections()[0] ?? null;
            if ($section) {
                $elements = $section->getElements();
                foreach ($elements as $element) {
                    if (method_exists($element, 'getText')) {
                        $text = $element->getText();
                        $pdf->MultiCell(190, 10, $text ?? '');
                    }
                }
            }
            
            // Save PDF
            $pdfFilename = $filename . '_converted.pdf';
            $pdfPath = 'temp/' . uniqid() . '.pdf';
            $fullPdfPath = Storage::disk('public')->path($pdfPath);
            
            $pdf->Output($fullPdfPath, 'F');
            
            // Clean up Word file
            Storage::disk('public')->delete($wordPath);
            
            return response()->download($fullPdfPath, $pdfFilename)->deleteFileAfterSend(true);
            
        } catch (Exception $e) {
            Log::error('Word to PDF conversion error: ' . $e->getMessage());
            return back()->with('error', 'Conversion failed: ' . $e->getMessage());
        }
    }

    /**
     * Convert Book to Audio (Text to Speech)
     */
    public function bookToAudio(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf,txt|max:20480' // 20MB max
        ]);

        try {
            $file = $request->file('file');
            $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            
            // Extract text from file
            $text = '';
            $extension = $file->getClientOriginalExtension();
            
            if ($extension === 'txt') {
                $text = file_get_contents($file->getRealPath());
            } else if ($extension === 'pdf') {
                $parser = new Parser();
                $pdf = $parser->parseFile($file->getRealPath());
                $text = $pdf->getText();
            }
            
            // Clean and limit text (for demo purposes)
            $text = strip_tags($text);
            $text = preg_replace('/\s+/', ' ', $text);
            $text = trim($text);
            
            // Limit to first 5000 characters for demo
            if (strlen($text) > 5000) {
                $text = substr($text, 0, 5000) . '... [Content truncated for demo]';
            }
            
            // Use Google Text-to-Speech (free alternative)
            $audioContent = $this->textToSpeech($text);
            
            if (!$audioContent) {
                // Fallback: Create a simple MP3 with gTTS (requires Python)
                // Or use a free API
                return back()->with('error', 'Text-to-Speech service unavailable. Please try again later.');
            }
            
            // Save audio file
            $audioFilename = $filename . '_audio.mp3';
            $audioPath = 'temp/' . uniqid() . '.mp3';
            $fullAudioPath = Storage::disk('public')->path($audioPath);
            
            file_put_contents($fullAudioPath, $audioContent);
            
            return response()->download($fullAudioPath, $audioFilename)->deleteFileAfterSend(true);
            
        } catch (Exception $e) {
            Log::error('Book to Audio conversion error: ' . $e->getMessage());
            return back()->with('error', 'Conversion failed: ' . $e->getMessage());
        }
    }

    /**
     * Extract text from PDF page (helper method)
     */
    private function extractTextFromPdf($pdfPath, $pageNumber)
    {
        try {
            $parser = new Parser();
            $pdf = $parser->parseFile($pdfPath);
            $pages = $pdf->getPages();
            
            if (isset($pages[$pageNumber - 1])) {
                return $pages[$pageNumber - 1]->getText();
            }
            
            return null;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Convert text to speech using Google Cloud TTS or alternative
     */
    private function textToSpeech($text)
    {
        // Option 1: Use Google Cloud Text-to-Speech (requires API key)
        // Option 2: Use a free API like VoiceRSS
        
        // Using VoiceRSS (free tier available)
        $apiKey = '86de743728784305b383424684be542e'; // Get from https://www.voicerss.org/
        $language = 'en-us';
        $voice = 'Linda';
        
        $url = "https://api.voicerss.org/?key={$apiKey}&hl={$language}&v={$voice}&src=" . urlencode($text);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $audioContent = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode === 200 && $audioContent) {
            return $audioContent;
        }
        
        return false;
    }
}