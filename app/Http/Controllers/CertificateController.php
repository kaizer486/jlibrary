<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Certificate;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CertificateController extends Controller
{
    /**
     * Display all user certificates.
     */
    public function index()
    {
        $certificates = Certificate::where('user_id', Auth::id())
            ->with(['book', 'quiz'])
            ->latest()
            ->get();

        return view('certificates.index', compact('certificates'));
    }

    /**
     * Generate certificate after passing quiz.
     */
    public function generate(Book $book)
    {
        $score = session('quiz_score');
        $total = session('quiz_total');
        $percentage = session('quiz_percentage');

        if (!$score || $percentage < 70) {
            return redirect()->route('library.show', $book)
                ->with('error', 'You need to pass the quiz with 70% or higher to get a certificate.');
        }

        $existingCertificate = Certificate::where('user_id', Auth::id())
            ->where('book_id', $book->id)
            ->first();

        if ($existingCertificate) {
            return redirect()->route('certificates.index')
                ->with('info', 'You already have a certificate for this book.');
        }

        $certificate = $this->createBookCertificate(
            $book->id,
            $book->title,
            $book->author,
            $score,
            $total,
            $percentage
        );

        session()->forget(['quiz_score', 'quiz_total', 'quiz_percentage', 'quiz_book_id', 'quiz_book_title']);

        return redirect()->route('certificates.show', $certificate)
            ->with('success', 'Congratulations! Your certificate has been generated.');
    }

    /**
     * Generate certificate from quiz attempt.
     */
    public function generateFromQuiz($quiz, $attempt)
    {
        $existingCertificate = Certificate::where('user_id', Auth::id())
            ->where('quiz_id', $attempt->quiz_id)
            ->where('quiz_attempt_id', $attempt->id)
            ->first();

        if ($existingCertificate) {
            return $existingCertificate;
        }

        $bookTitle = $quiz->book_title ?? $quiz->title;
        $bookAuthor = $quiz->book_author ?? 'JLIBRARY';

        $certificate = $this->createQuizCertificate(
            $attempt->quiz_id,
            $attempt->id,
            $bookTitle,
            $bookAuthor,
            $attempt->score,
            $attempt->total_points,
            $attempt->percentage,
            $quiz->institution_id ?? null
        );

        return $certificate;
    }

    /**
     * Generate certificate from book completion.
     * AUTO-TRIGGERED when user reaches 100% progress.
     */
    public function generateFromBook($book, $progress = 100)
    {
        // Check if certificate already exists
        $existingCertificate = Certificate::where('user_id', Auth::id())
            ->where('book_id', $book->id)
            ->first();

        if ($existingCertificate) {
            return $existingCertificate;
        }

        // Generate certificate with 100% score for book completion
        $certificate = $this->createBookCertificate(
            $book->id,
            $book->title,
            $book->author,
            100,
            100,
            100
        );

        return $certificate;
    }

    /**
     * Create a book certificate record and PDF.
     */
    private function createBookCertificate(
        $bookId,
        string $title,
        string $author,
        int $score,
        int $total,
        float $percentage
    ) {
        // Generate unique certificate number
        $certificateNumber = 'JLIB-' . strtoupper(Str::random(8)) . '-' . Auth::id();

        // Generate PDF
        $pdf = $this->generatePDF(
            $title,
            $author,
            $score,
            $total,
            $percentage,
            $certificateNumber
        );

        // Save PDF to storage
        $fileName = 'certificates/certificate_' . Auth::id() . '_' . time() . '_' . Str::random(6) . '.pdf';
        Storage::disk('public')->put($fileName, $pdf);

        // Create certificate record
        $certificate = Certificate::create([
            'user_id' => Auth::id(),
            'book_id' => $bookId,
            'quiz_id' => null,
            'quiz_attempt_id' => null,
            'institution_id' => null,
            'certificate_number' => $certificateNumber,
            'file_path' => $fileName,
            'quiz_score' => $score,
            'total_questions' => $total,
            'percentage' => $percentage,
        ]);

        return $certificate;
    }

    /**
     * Create a quiz certificate record and PDF.
     */
    private function createQuizCertificate(
        $quizId,
        $quizAttemptId,
        string $title,
        string $author,
        int $score,
        int $total,
        float $percentage,
        $institutionId = null
    ) {
        // Generate unique certificate number
        $certificateNumber = 'JLIB-' . strtoupper(Str::random(8)) . '-' . Auth::id();

        // Generate PDF
        $pdf = $this->generatePDF(
            $title,
            $author,
            $score,
            $total,
            $percentage,
            $certificateNumber
        );

        // Save PDF to storage
        $fileName = 'certificates/certificate_' . Auth::id() . '_' . time() . '_' . Str::random(6) . '.pdf';
        Storage::disk('public')->put($fileName, $pdf);

        // Create certificate record
        $certificate = Certificate::create([
            'user_id' => Auth::id(),
            'book_id' => null,
            'quiz_id' => $quizId,
            'quiz_attempt_id' => $quizAttemptId,
            'institution_id' => $institutionId,
            'certificate_number' => $certificateNumber,
            'file_path' => $fileName,
            'quiz_score' => $score,
            'total_questions' => $total,
            'percentage' => $percentage,
        ]);

        return $certificate;
    }

    /**
     * Generate PDF with certificate image as background.
     * ONLY overlays user name, book title, and author.
     */
   
   
   private function generatePDF(
    string $bookTitle,
    string $bookAuthor,
    int $score,
    int $total,
    float $percentage,
    string $certificateNumber
) {
    $user = Auth::user();

    // Convert image to base64
    $imagePath = public_path('images/a.jpeg');
    $imageData = 'data:image/jpeg;base64,' . base64_encode(file_get_contents($imagePath));

    // ADD A VERSION MARKER TO CONFIRM NEW CODE RUNS
    $version = 'v2-' . date('Y-m-d H:i:s');

    $html = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Certificate</title>
        <style>
            @page { margin: 0; padding: 0; size: a4 landscape; }
            body { margin: 0; padding: 0; font-family: "Georgia", serif; background: white; }
            .wrapper {
                position: relative;
                width: 842pt;
                height: 595pt;
            }
            .bg {
                position: absolute;
                top: 0;
                left: 0;
                width: 842pt;
                height: 595pt;
                object-fit: cover;
            }
            .content {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: flex-start;
                text-align: center;
                z-index: 10;
                padding-top: 200px;
            }
            .name {
                font-size: 52px;
                font-weight: 700;
                color: #1a202c;
                letter-spacing: 3px;
                text-transform: uppercase;
                margin-bottom: 15px;
            }
            .book {
                font-size: 36px;
                font-weight: 600;
                color: #2d3748;
                margin-bottom: 8px;
            }
            .author {
                font-size: 24px;
                font-weight: 400;
                color: #4a5568;
                font-style: italic;
            }
            .version {
                position: absolute;
                bottom: 20px;
                right: 30px;
                font-size: 10px;
                color: #ccc;
                z-index: 10;
            }
        </style>
    </head>
    <body>
        <div class="wrapper">
            <img src="' . $imageData . '" class="bg" alt="Background">
            <div class="content">
                <div class="name">' . $user->full_name . '</div>
                <div class="book">"' . $bookTitle . '"</div>
                <div class="author">by ' . $bookAuthor . '</div>
            </div>
            <div class="version">' . $version . '</div>
        </div>
    </body>
    </html>
    ';

    // Generate PDF
    $pdf = Pdf::loadHTML($html);
    $pdf->setPaper('a4', 'landscape');
    $pdf->setOptions([
        'defaultFont' => 'Helvetica',
        'isRemoteEnabled' => true,
        'isHtml5ParserEnabled' => true,
        'isPhpEnabled' => true,
    ]);

    return $pdf->output();
}
    /**
     * Show single certificate.
     */
    public function show(Certificate $certificate)
    {
        if ($certificate->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }

        return view('certificates.show', compact('certificate'));
    }

    /**
     * Auto-regenerate certificate file if missing.
     */
    public function regenerateCertificateFile(Certificate $certificate)
    {
        $bookTitle = $certificate->book->title ?? 'Certificate';
        $bookAuthor = $certificate->book->author ?? 'JLIBRARY';

        $pdf = $this->generatePDF(
            $bookTitle,
            $bookAuthor,
            $certificate->quiz_score,
            $certificate->total_questions,
            $certificate->percentage,
            $certificate->certificate_number
        );

        $fileName = 'certificates/certificate_' . $certificate->user_id . '_' . $certificate->id . '_' . time() . '.pdf';
        Storage::disk('public')->put($fileName, $pdf);

        $certificate->file_path = $fileName;
        $certificate->save();

        return $certificate;
    }

 public function download(Certificate $certificate)
{
    if ($certificate->user_id !== Auth::id()) {
        abort(403, 'Unauthorized access.');
    }

    $filePath = storage_path('app/public/' . $certificate->file_path);

    if (!file_exists($filePath)) {
        // Try public path
        $filePath = public_path('storage/' . $certificate->file_path);
        if (!file_exists($filePath)) {
            abort(404, 'Certificate file not found.');
        }
    }

    return response()->download($filePath, 'certificate_' . $certificate->certificate_number . '.pdf');
}
/**
 * Serve the certificate PDF directly (bypasses storage link issues).
 */
public function serve($id)
{
    $certificate = Certificate::findOrFail($id);
    
    if ($certificate->user_id !== Auth::id()) {
        abort(403, 'Unauthorized access.');
    }
    
    // Try storage path first
    $filePath = storage_path('app/public/' . $certificate->file_path);
    
    // If not found, try public path
    if (!file_exists($filePath)) {
        $filePath = public_path('storage/' . $certificate->file_path);
    }
    
    if (!file_exists($filePath)) {
        abort(404, 'Certificate file not found.');
    }
    
    return response()->file($filePath, [
        'Content-Type' => 'application/pdf',
        'Content-Disposition' => 'inline; filename="certificate_' . $certificate->certificate_number . '.pdf"',
    ]);
}

    /**
     * Regenerate certificate PDF (admin only).
     */
    public function regenerate(Certificate $certificate)
    {
        if (!auth()->user()->isSuperAdmin() && !auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        $bookTitle = $certificate->book->title ?? 'Certificate';
        $bookAuthor = $certificate->book->author ?? 'JLIBRARY';

        $pdf = $this->generatePDF(
            $bookTitle,
            $bookAuthor,
            $certificate->quiz_score,
            $certificate->total_questions,
            $certificate->percentage,
            $certificate->certificate_number
        );

        Storage::disk('public')->put($certificate->file_path, $pdf);

        return redirect()->back()
            ->with('success', 'Certificate regenerated successfully.');
    }

    /**
     * Delete certificate (admin only).
     */
    public function destroy(Certificate $certificate)
    {
        if (!auth()->user()->isSuperAdmin() && !auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        if ($certificate->file_path) {
            Storage::disk('public')->delete($certificate->file_path);
        }

        $certificate->delete();

        return redirect()->route('certificates.index')
            ->with('success', 'Certificate deleted successfully.');
    }
}