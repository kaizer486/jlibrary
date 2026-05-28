<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Transaction;  
use App\Models\MarketplaceListing;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WalletController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $balance = $user->wallet_balance ?? 0;
        
        // Get transaction history from both Payment and Transaction models
        $payments = Payment::where('user_id', $user->id)
                           ->latest()
                           ->limit(10)
                           ->get();
        
        $transactions = Transaction::where('user_id', $user->id)
                                   ->latest()
                                   ->limit(10)
                                   ->get();
        
        // Combine and sort all transactions
        $allTransactions = $payments->concat($transactions)
                                    ->sortByDesc('created_at');
        
        // Calculate marketplace earnings (80% of sales)
        $marketplaceEarnings = MarketplaceListing::where('seller_id', $user->id)
                                                 ->where('status', 'approved')
                                                 ->sum('price') * 0.8;
        
        // Calculate total spent on purchases
        $totalSpent = Payment::where('user_id', $user->id)
                             ->where('status', 'completed')
                             ->where('payable_type', 'App\Models\Book')
                             ->sum('amount');
        
        // Calculate total deposits
        $totalDeposits = Payment::where('user_id', $user->id)
                                ->where('status', 'completed')
                                ->where('payable_type', 'App\\Models\\User')
                                ->sum('amount');
        
        // Calculate pending withdrawals
        $pendingWithdrawals = Transaction::where('user_id', $user->id)
                                         ->where('type', 'debit')
                                         ->where('status', 'pending')
                                         ->sum('amount');
        
        // Calculate total withdrawn
        $totalWithdrawn = Transaction::where('user_id', $user->id)
                                     ->where('type', 'debit')
                                     ->where('status', 'completed')
                                     ->sum('amount');
        
        $referralEarnings = $user->referral_earnings ?? 0;
        $availableToWithdraw = $balance - $pendingWithdrawals;
        
        $recentSales = MarketplaceListing::where('seller_id', $user->id)
                                         ->where('status', 'approved')
                                         ->latest()
                                         ->limit(5)
                                         ->get();
        
        return view('wallet.index', compact(
            'balance', 
            'transactions',
            'allTransactions',
            'payments',
            'marketplaceEarnings', 
            'totalSpent',
            'totalDeposits',
            'totalWithdrawn',
            'pendingWithdrawals',
            'availableToWithdraw',
            'referralEarnings',
            'recentSales'
        ));
    }
    public function withdraw(Request $request)
{
    $user = Auth::user();
    $currentBalance = $user->wallet_balance ?? 0;
    
    // Set minimum withdrawal amount
 $minWithdrawal = config('wallet.withdrawal.min_amount', 5000);
    
    // Check if user has enough balance
    if ($currentBalance < $minWithdrawal) {
        return redirect()->back()->with('error', 'Minimum withdrawal amount is TSh ' . number_format($minWithdrawal, 2) . '. Your current balance is TSh ' . number_format($currentBalance, 2));
    }
    
    $request->validate([
        'amount' => 'required|numeric|min:' . $minWithdrawal . '|max:' . $currentBalance,
        'method' => 'required|in:mpesa,bank',
        'phone' => 'required_if:method,mpesa|nullable|regex:/^[0-9]{10}$/',
        'account_name' => 'required_if:method,bank|nullable|string|max:255',
        'account_number' => 'required_if:method,bank|nullable|string|max:50',
        'bank_name' => 'required_if:method,bank|nullable|string|max:255',
    ], [
        'amount.min' => 'Minimum withdrawal amount is TSh ' . number_format($minWithdrawal, 2),
        'amount.max' => 'You cannot withdraw more than your current balance of TSh ' . number_format($currentBalance, 2),
        'amount.required' => 'Please enter an amount to withdraw',
        'amount.numeric' => 'Amount must be a number',
        'phone.regex' => 'Please enter a valid 10-digit phone number (e.g., 0712345678)',
    ]);

        $amount = $request->amount;
        
        DB::beginTransaction();
        
        try {
            // Create pending withdrawal transaction
         $transaction = \App\Models\Transaction::create([
    'user_id' => $user->id,
    'type' => 'debit',
    'amount' => $amount,
    'balance_after' => $user->wallet_balance - $amount,
    'description' => 'Withdrawal request via ' . $request->payment_method,
    'reference' => 'WTD_' . time() . '_' . $user->id,
    'status' => 'pending',
    'method' => $request->payment_method,
    'payable_type' => 'App\\Models\\User',
    'payable_id' => $user->id,
]);
            
            // Deduct from wallet (hold the amount)
            $user->wallet_balance = $user->wallet_balance - $amount;
            $user->save();
            
            DB::commit();
            
            return redirect()->back()->with('success', 'Withdrawal request submitted successfully! Admin will process within 24 hours.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to process withdrawal: ' . $e->getMessage());
        }
    }
    
    public function topUp(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1000|max:1000000',
        ], [
            'amount.min' => 'Minimum top-up amount is TSh 1,000',
            'amount.max' => 'Maximum top-up amount is TSh 1,000,000',
            'amount.required' => 'Please enter an amount to add',
            'amount.numeric' => 'Amount must be a number',
        ]);
        
        // Redirect to payment methods page with amount pre-filled
        return redirect()->route('payment.methods', ['amount' => $request->amount]);
    }
    
    /**
     * Get wallet balance (AJAX)
     */
    public function getBalance()
    {
        return response()->json([
            'balance' => auth()->user()->wallet_balance,
            'formatted' => 'TSh ' . number_format(auth()->user()->wallet_balance, 2)
        ]);
    }
}