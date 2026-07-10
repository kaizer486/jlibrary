<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookshopBook;
use App\Models\Payment;
use App\Models\Transaction;
use App\Models\User;
use App\Services\CommissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BookPurchaseController extends Controller
{
    protected $commissionService;
    
    public function __construct(CommissionService $commissionService)
    {
        $this->commissionService = $commissionService;
    }

    /**
     * Show the purchase page for a book.
     */
    public function purchase($bookId)
    {
        // ✅ Try to find the book in both tables
        $book = Book::find($bookId);
        if (!$book) {
            $book = BookshopBook::find($bookId);
        }
        
        if (!$book) {
            abort(404, 'Book not found.');
        }

        // ✅ Check if user already owns this book
        $user = Auth::user();
        if ($user && $user->hasPurchasedBook($book->id)) {
            return redirect()->back()->with('info', 'You already own this book.');
        }

        // ✅ If free, add to library directly
        if (!$book->is_paid || $book->price <= 0) {
            if ($user) {
                $this->addBookToLibrary($user, $book);
                return redirect()->route('library.index')->with('success', 'Free book added to your library!');
            }
            return redirect()->route('login')->with('info', 'Please login to access this free book.');
        }

        // ✅ Get wallet balance
        $walletBalance = $user ? $user->wallet_balance : 0;

        return view('book.purchase', compact('book', 'walletBalance'));
    }

    /**
     * Process purchase with wallet.
     */
    public function purchaseWithWallet(Request $request, $bookId)
    {
        $user = Auth::user();
        
        // ✅ Try to find the book in both tables
        $book = Book::find($bookId);
        if (!$book) {
            $book = BookshopBook::find($bookId);
        }
        
        if (!$book) {
            return response()->json([
                'success' => false,
                'message' => 'Book not found.'
            ], 404);
        }
        
        // Check if user already owns the book
        if ($user->hasPurchasedBook($book->id)) {
            return response()->json([
                'success' => false,
                'message' => 'You already own this book.'
            ], 400);
        }
        
        // Check if free book
        if (!$book->is_paid || $book->price <= 0) {
            $this->addBookToLibrary($user, $book);
            return response()->json([
                'success' => true,
                'message' => 'Free book added to your library!'
            ]);
        }
        
        // Check wallet balance
        if ($user->wallet_balance < $book->price) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient wallet balance',
                'shortfall' => $book->price - $user->wallet_balance
            ], 400);
        }
        
        DB::beginTransaction();
        
        try {
            // Lock user row
            $lockedUser = User::where('id', $user->id)->lockForUpdate()->first();
            
            // Deduct from wallet
            $oldBalance = $lockedUser->wallet_balance;
            $newBalance = $oldBalance - $book->price;
            $lockedUser->wallet_balance = $newBalance;
            $lockedUser->save();
            
            // Create payment record
            $payment = Payment::create([
                'user_id' => $lockedUser->id,
                'payable_type' => Book::class,
                'payable_id' => $book->id,
                'amount' => $book->price,
                'status' => 'completed',
                'method' => 'wallet',
                'reference' => 'PUR_' . time() . '_' . $lockedUser->id . '_' . $book->id,
            ]);
            
            // Create transaction record
            Transaction::create([
                'user_id' => $lockedUser->id,
                'type' => 'debit',
                'amount' => $book->price,
                'balance_after' => $newBalance,
                'description' => 'Purchase: ' . $book->title,
                'reference' => $payment->reference,
                'status' => 'completed',
                'method' => 'wallet',
                'payable_type' => Book::class,
                'payable_id' => $book->id,
            ]);
            
            // ✅ ADD COMMISSION LOGIC - If book has an author
            if ($book->author_id) {
                $author = User::find($book->author_id);
                if ($author) {
                    $this->commissionService->processCommission(
                        $author,
                        $lockedUser,
                        $book,
                        $book->price
                    );
                }
            }
            
            // Add book to user's library
            $lockedUser->books()->syncWithoutDetaching([
                $book->id => [
                    'purchased_at' => now(),
                    'status' => 'want_to_read'
                ]
            ]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Book purchased successfully!',
                'new_balance' => $newBalance,
                'redirect' => route('library.index')
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Purchase failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
 * Process purchase with external payment method.
 */
/**
 * Process purchase with external payment method.
 */
/**
 * Process purchase with external payment method.
 */
public function processPurchase(Request $request)
{
    $request->validate([
        'book_id' => 'required|integer',
        'payment_method' => 'required|string|in:wallet,mpesa,tigopesa,halopesa,pesapal,bank'
    ]);

    $user = Auth::user();
    $bookId = $request->book_id;
    
    // Try to find the book
    $book = Book::find($bookId);
    if (!$book) {
        $book = BookshopBook::find($bookId);
    }
    
    if (!$book) {
        return redirect()->back()->with('error', 'Book not found.');
    }

    // Check if user already owns the book
    if ($user->hasPurchasedBook($book->id)) {
        return redirect()->back()->with('error', 'You already own this book.');
    }

    // If wallet, process directly
    if ($request->payment_method === 'wallet') {
        return $this->purchaseWithWallet($request, $bookId);
    }

    // For external payments, create pending payment
    $payment = Payment::create([
        'user_id' => $user->id,
        'payable_type' => Book::class,
        'payable_id' => $book->id,
        'amount' => $book->price,
        'status' => 'pending',
        'method' => $request->payment_method,
        'reference' => strtoupper($request->payment_method) . '_' . time() . '_' . $user->id . '_' . $book->id,
    ]);

    // ✅ Show payment instructions page with correct parameter name
    return redirect()->route('payment.instructions', [
        'paymentId' => $payment->id,
        'method' => $request->payment_method
    ]);
}

/**
 * Confirm payment after user has made the payment.
 */
public function confirmPayment(Request $request)
{
    $request->validate([
        'payment_id' => 'required|integer|exists:payments,id'
    ]);

    $user = auth()->user();
    $payment = Payment::where('user_id', $user->id)
        ->where('id', $request->payment_id)
        ->where('status', 'pending')
        ->first();

    if (!$payment) {
        return response()->json([
            'success' => false,
            'message' => 'Payment not found or already processed.'
        ], 404);
    }

    // ✅ For now, mark as completed (in production, verify with payment provider)
    $payment->update(['status' => 'completed']);

    // ✅ Add book to user's library
    $book = Book::find($payment->payable_id);
    if (!$book) {
        $book = BookshopBook::find($payment->payable_id);
    }
    
    if ($book) {
        $user->books()->syncWithoutDetaching([
            $book->id => [
                'purchased_at' => now(),
                'status' => 'want_to_read'
            ]
        ]);
    }

    // ✅ Add transaction record
    \App\Models\Transaction::create([
        'user_id' => $user->id,
        'type' => 'debit',
        'amount' => $payment->amount,
        'balance_after' => $user->wallet_balance ?? 0,
        'description' => 'Book Purchase: ' . ($book->title ?? 'Book'),
        'reference' => $payment->reference,
        'status' => 'completed',
        'method' => $payment->method,
        'payable_type' => Book::class,
        'payable_id' => $book->id ?? $payment->payable_id,
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Payment confirmed! Book added to your library.'
    ]);
}
/**
 * Show payment instructions.
 */
public function showPaymentInstructions($paymentId)
{
    $payment = Payment::where('user_id', auth()->id())
        ->where('id', $paymentId)
        ->firstOrFail();
    
    // Get the book for the return link
    $book = Book::find($payment->payable_id);
    if (!$book) {
        $book = BookshopBook::find($payment->payable_id);
    }
    
    return view('payment.instructions', compact('payment', 'book'));
}
    /**
     * Show purchase success page.
     */
    public function purchaseSuccess($paymentId)
    {
        $user = Auth::user();
        $payment = Payment::where('user_id', $user->id)
            ->where('id', $paymentId)
            ->where('status', 'completed')
            ->firstOrFail();

        // Get the book
        $book = Book::find($payment->payable_id);
        if (!$book) {
            $book = BookshopBook::find($payment->payable_id);
        }

        return view('book.purchase-success', compact('payment', 'book'));
    }

    /**
     * Show purchase history.
     */
    public function purchaseHistory()
    {
        $user = Auth::user();
        $purchases = Payment::where('user_id', $user->id)
            ->where('status', 'completed')
            ->whereIn('payable_type', [Book::class, BookshopBook::class])
            ->latest()
            ->paginate(10);

        return view('book.purchase-history', compact('purchases'));
    }

    /**
     * Download a purchased book.
     */
   /**
 * Download a purchased book.
 */
public function downloadBook($bookId)
{
    $user = Auth::user();

    // Check if user has purchased this book OR if book is free
    $book = Book::find($bookId);
    if (!$book) {
        $book = BookshopBook::find($bookId);
    }

    if (!$book) {
        abort(404, 'Book not found.');
    }

    // Allow download if book is free OR user purchased it
    $isFree = $book->price <= 0;
    $hasPurchased = $user->hasPurchasedBook($book->id);
    
    if (!$isFree && !$hasPurchased) {
        return redirect()->back()->with('error', 'You need to purchase this book to download it.');
    }

    if (!$book->file_path) {
        return redirect()->back()->with('error', 'No file available for download.');
    }

    // ✅ Check if file actually exists
    $filePath = storage_path('app/public/' . $book->file_path);
    
    if (!file_exists($filePath)) {
        // ✅ Log the error
        \Log::error('Book file not found', [
            'book_id' => $book->id,
            'file_path' => $book->file_path,
            'user_id' => $user->id
        ]);
        
        return redirect()->back()->with('error', 'The file for this book is currently unavailable. Please contact support.');
    }

    // Increment downloads
    if (isset($book->downloads)) {
        $book->increment('downloads');
    }

    return response()->download($filePath);
}
/**
 * Check if book file exists.
 */
public function checkFileExists($bookId)
{
    $book = Book::find($bookId);
    if (!$book) {
        $book = BookshopBook::find($bookId);
    }
    
    if (!$book) {
        return response()->json(['exists' => false, 'message' => 'Book not found'], 404);
    }
    
    if (!$book->file_path) {
        return response()->json(['exists' => false, 'message' => 'No file attached to this book']);
    }
    
    $filePath = storage_path('app/public/' . $book->file_path);
    $exists = file_exists($filePath);
    
    return response()->json([
        'exists' => $exists,
        'path' => $book->file_path,
        'full_path' => $filePath
    ]);
}
    /**
     * Add book to user's library.
     */
    private function addBookToLibrary($user, $book)
    {
        $user->books()->syncWithoutDetaching([
            $book->id => [
                'purchased_at' => now(),
                'status' => 'want_to_read'
            ]
        ]);
    }
}