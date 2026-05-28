<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Certificate;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CertificateController extends Controller
{
    // Show all user certificates
    public function index()
    {
        $certificates = Certificate::where('user_id', Auth::id())
                                   ->with('book')
                                   ->latest()
                                   ->get();
        
        return view('certificates.index', compact('certificates'));
    }
    
    // Generate certificate after passing quiz
    public function generate(Book $book)
    {
        // Get quiz results from session
        $score = session('quiz_score');
        $total = session('quiz_total');
        $percentage = session('quiz_percentage');
        
        // Check if user passed (70% or higher)
        if (!$score || $percentage < 70) {
            return redirect()->route('library.show', $book)
                            ->with('error', 'You need to pass the quiz with 70% or higher to get a certificate.');
        }
        
        // Check if certificate already exists
        $existingCertificate = Certificate::where('user_id', Auth::id())
                                          ->where('book_id', $book->id)
                                          ->first();
        
        if ($existingCertificate) {
            return redirect()->route('certificates.index')
                            ->with('info', 'You already have a certificate for this book.');
        }
        
        // Generate unique certificate number
        $certificateNumber = 'JLIB-' . strtoupper(uniqid()) . '-' . Auth::id();
        
        // Generate PDF
        $pdf = $this->generatePDF($book, $score, $total, $percentage, $certificateNumber);
        
        // Save PDF to storage
        $fileName = 'certificates/certificate_' . Auth::id() . '_' . $book->id . '_' . time() . '.pdf';
        Storage::disk('public')->put($fileName, $pdf);
        
        // Save certificate record to database
        $certificate = Certificate::create([
            'user_id' => Auth::id(),
            'book_id' => $book->id,
            'certificate_number' => $certificateNumber,
            'file_path' => $fileName,
            'quiz_score' => $score,
            'total_questions' => $total,
            'percentage' => $percentage
        ]);
        
        // Clear quiz session
        session()->forget(['quiz_score', 'quiz_total', 'quiz_percentage', 'quiz_book_id', 'quiz_book_title']);
        
        return redirect()->route('certificates.show', $certificate)
                         ->with('success', 'Congratulations! Your certificate has been generated.');
    }
    
    // Show single certificate
    public function show(Certificate $certificate)
    {
        // Check if certificate belongs to logged in user
        if ($certificate->user_id !== Auth::id()) {
            abort(403);
        }
        
        return view('certificates.show', compact('certificate'));
    }
    
    // Download certificate PDF
    public function download(Certificate $certificate)
    {
        // Check if certificate belongs to logged in user
        if ($certificate->user_id !== Auth::id()) {
            abort(403);
        }
        
        $filePath = storage_path('app/public/' . $certificate->file_path);
        
        if (!file_exists($filePath)) {
            abort(404, 'Certificate file not found.');
        }
        
        return response()->download($filePath, 'certificate_' . $certificate->certificate_number . '.pdf');
    }
    
    // Generate PDF content
    private function generatePDF($book, $score, $total, $percentage, $certificateNumber)
    {
        $user = Auth::user();
        $date = now()->format('F d, Y');
        
        $data = [
            'user_name' => $user->full_name,
            'book_title' => $book->title,
            'book_author' => $book->author,
            'score' => $score,
            'total' => $total,
            'percentage' => $percentage,
            'certificate_number' => $certificateNumber,
            'date' => $date,
        ];
        
        $pdf = Pdf::loadView('certificates.template', $data);
        $pdf->setPaper('a4', 'landscape');
        
        return $pdf->output();
    }
}