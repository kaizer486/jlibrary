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

        // Generate certificate
        $certificate = $this->createCertificate(
            bookId: $book->id,
            title: $book->title,
            author: $book->author,
            score: $score,
            total: $total,
            percentage: $percentage
        );

        // Clear quiz session
        session()->forget(['quiz_score', 'quiz_total', 'quiz_percentage', 'quiz_book_id', 'quiz_book_title']);

        return redirect()->route('certificates.show', $certificate)
            ->with('success', 'Congratulations! Your certificate has been generated.');
    }

    /**
     * Generate certificate from quiz attempt.
     */
    public function generateFromQuiz($quiz, $attempt)
    {
        // Check if certificate already exists
        $existingCertificate = Certificate::where('user_id', Auth::id())
            ->where('quiz_id', $attempt->quiz_id)
            ->where('quiz_attempt_id', $attempt->id)
            ->first();

        if ($existingCertificate) {
            return $existingCertificate;
        }

        // Use book info or quiz info
        $bookTitle = $quiz->book_title ?? $quiz->title;
        $bookAuthor = $quiz->book_author ?? 'JLIBRARY';

        // Generate certificate
        $certificate = $this->createCertificate(
            quizId: $attempt->quiz_id,
            quizAttemptId: $attempt->id,
            title: $bookTitle,
            author: $bookAuthor,
            score: $attempt->score,
            total: $attempt->total_points,
            percentage: $attempt->percentage,
            institutionId: $quiz->institution_id ?? null
        );

        return $certificate;
    }

    /**
     * Generate certificate from book completion.
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

        // Generate certificate
        $certificate = $this->createCertificate(
            bookId: $book->id,
            title: $book->title,
            author: $book->author,
            score: $progress,
            total: 100,
            percentage: $progress,
            institutionId: $book->institution_id ?? null
        );

        return $certificate;
    }

    /**
     * Create a certificate record and PDF.
     */
    private function createCertificate(
        $bookId = null,
        $quizId = null,
        $quizAttemptId = null,
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
            bookTitle: $title,
            bookAuthor: $author,
            score: $score,
            total: $total,
            percentage: $percentage,
            certificateNumber: $certificateNumber
        );

        // Save PDF to storage
        $fileName = 'certificates/certificate_' . Auth::id() . '_' . time() . '_' . Str::random(6) . '.pdf';
        Storage::disk('public')->put($fileName, $pdf);

        // Create certificate record
        $certificate = Certificate::create([
            'user_id' => Auth::id(),
            'book_id' => $bookId,
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
     * Generate PDF content.
     */
    private function generatePDF(string $bookTitle, string $bookAuthor, int $score, int $total, float $percentage, string $certificateNumber)
    {
        $user = Auth::user();
        $date = now()->format('F d, Y');

        $data = [
            'user_name' => $user->full_name,
            'book_title' => $bookTitle,
            'book_author' => $bookAuthor,
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


    
    /**
     * Show single certificate.
     */
    public function show(Certificate $certificate)
    {
        // Check if certificate belongs to logged in user
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
        bookTitle: $bookTitle,
        bookAuthor: $bookAuthor,
        score: $certificate->quiz_score,
        total: $certificate->total_questions,
        percentage: $certificate->percentage,
        certificateNumber: $certificate->certificate_number
    );

    $fileName = 'certificates/certificate_' . $certificate->user_id . '_' . $certificate->id . '_' . time() . '.pdf';
    Storage::disk('public')->put($fileName, $pdf);

    $certificate->file_path = $fileName;
    $certificate->save();

    return $certificate;
}

    /**
     * Download certificate PDF.
     */
    public function download(Certificate $certificate)
    {
        // Check if certificate belongs to logged in user
        if ($certificate->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }

        $filePath = storage_path('app/public/' . $certificate->file_path);

        if (!file_exists($filePath)) {
            abort(404, 'Certificate file not found.');
        }

        return response()->download($filePath, 'certificate_' . $certificate->certificate_number . '.pdf');
    }

    /**
     * Regenerate certificate PDF (admin only).
     */
    public function regenerate(Certificate $certificate)
    {
        // Only allow admin/super admin
        if (!auth()->user()->isSuperAdmin() && !auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        $bookTitle = $certificate->book->title ?? 'Certificate';
        $bookAuthor = $certificate->book->author ?? 'JLIBRARY';

        $pdf = $this->generatePDF(
            bookTitle: $bookTitle,
            bookAuthor: $bookAuthor,
            score: $certificate->quiz_score,
            total: $certificate->total_questions,
            percentage: $certificate->percentage,
            certificateNumber: $certificate->certificate_number
        );

        // Overwrite existing file
        Storage::disk('public')->put($certificate->file_path, $pdf);

        return redirect()->back()
            ->with('success', 'Certificate regenerated successfully.');
    }

    /**
     * Delete certificate (admin only).
     */
    public function destroy(Certificate $certificate)
    {
        // Only allow admin/super admin
        if (!auth()->user()->isSuperAdmin() && !auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        // Delete file
        if ($certificate->file_path) {
            Storage::disk('public')->delete($certificate->file_path);
        }

        $certificate->delete();

        return redirect()->route('certificates.index')
            ->with('success', 'Certificate deleted successfully.');
    }
}