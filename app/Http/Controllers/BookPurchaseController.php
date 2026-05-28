<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookPurchaseController extends Controller
{
    /**
     * Get purchase information for a book
     */
    public function purchaseInfo(Book $book)
    {
        $user = auth()->user();
        
        // Check if already purchased
        $alreadyPurchased = $book->isPurchasedByUser($user->id);
        
        return response()->json([
            'success' => true,
            'book' => [
                'id' => $book->id,
                'title' => $book->title,
                'price' => $book->price,
                'is_paid' => $book->is_paid,
                'already_purchased' => $alreadyPurchased
            ],
            'user' => [
                'wallet_balance' => $user->wallet_balance,
                'has_sufficient_funds' => $user->wallet_balance >= $book->price
            ]
        ]);
    }
    
    /**
     * Purchase a book using wallet balance
     */
   /**
 * Purchase a book using wallet balance
 */
public function purchaseWithWallet(Book $book)
{
    $user = auth()->user();
    
    // Check if book is paid
    if (!$book->is_paid) {
        return response()->json([
            'success' => false,
            'message' => 'This book is free. You can read it without purchase.'
        ], 400);
    }
    
    // Check if already purchased
    if ($book->isPurchasedByUser($user->id)) {
        return response()->json([
            'success' => false,
            'message' => 'You already own this book.'
        ], 400);
    }
    
    // Check if user has sufficient balance
    if ($user->wallet_balance < $book->price) {
        $shortfall = $book->price - $user->wallet_balance;
        
        return response()->json([
            'success' => false,
            'message' => 'Insufficient wallet balance',
            'error_type' => 'insufficient_balance',
            'shortfall' => $shortfall,
            'book_price' => $book->price,
            'current_balance' => $user->wallet_balance,
            'book_id' => $book->id,
            'book_title' => $book->title
        ], 400);
    }
    
    DB::beginTransaction();
    
    try {
        // Process the purchase using the user model method
        $result = $user->purchaseBookWithWallet($book, 'wallet');
        
        if (!$result['success']) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $result['message']
            ], 400);
        }
        
        DB::commit();
        
        return response()->json([
            'success' => true,
            'message' => $result['message'],
            'new_balance' => $result['new_balance'],
            'redirect_url' => route('library.read', $book)
        ]);
        
    } catch (\Exception $e) {
        DB::rollBack();
        
        return response()->json([
            'success' => false,
            'message' => 'An error occurred: ' . $e->getMessage()
        ], 500);
    }
}    
    /**
     * Check if user has purchased a book
     */
    public function checkPurchase(Book $book)
    {
        $user = auth()->user();
        
        return response()->json([
            'success' => true,
            'has_purchased' => $book->isPurchasedByUser($user->id),
            'can_access' => $book->canUserAccess($user->id)
        ]);
    }
}