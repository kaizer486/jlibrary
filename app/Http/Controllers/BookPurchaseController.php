<?php

namespace App\Http\Controllers;

use App\Models\Book;
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
     * Purchase book using wallet balance
     */
    public function purchaseWithWallet(Request $request, $bookId)
    {
        $user = Auth::user();
        $book = Book::findOrFail($bookId);
        
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
                'new_balance' => $newBalance
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Purchase failed: ' . $e->getMessage()
            ], 500);
        }
    }
    
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