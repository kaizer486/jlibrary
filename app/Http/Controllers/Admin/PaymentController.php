<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    /**
     * Display payments dashboard
     */
    public function index()
    {
        // Get all payments with user relationship
        $payments = Payment::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        // Get pending withdrawals
        $pendingWithdrawals = Transaction::where('type', 'debit')
            ->where('status', 'pending')
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Get completed withdrawals
        $completedWithdrawals = Transaction::where('type', 'debit')
            ->where('status', 'completed')
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();
        
        // Get deposit stats
        $totalDeposits = Payment::where('status', 'completed')
            ->where('payable_type', 'App\\Models\\User')
            ->sum('amount');
        
        $pendingDeposits = Payment::where('status', 'pending')
            ->where('payable_type', 'App\\Models\\User')
            ->sum('amount');
        
        $totalWithdrawals = Transaction::where('type', 'debit')
            ->where('status', 'completed')
            ->sum('amount');
        
        $totalBookSales = Payment::where('status', 'completed')
            ->where('payable_type', 'App\\Models\\Book')
            ->sum('amount');
        
        return view('admin.payments.index', compact(
            'payments',
            'pendingWithdrawals',
            'completedWithdrawals',
            'totalDeposits',
            'pendingDeposits',
            'totalWithdrawals',
            'totalBookSales'
        ));
    }
    
    /**
     * Show single payment details
     */
    public function show($id)
    {
        $payment = Payment::with('user')->findOrFail($id);
        
        return view('admin.payments.show', compact('payment'));
    }
    
    /**
     * Approve a pending withdrawal
     */
    public function approveWithdrawal($id)
    {
        $transaction = Transaction::findOrFail($id);
        
        if ($transaction->status !== 'pending') {
            return back()->with('error', 'This withdrawal has already been processed.');
        }
        
        DB::beginTransaction();
        
        try {
            $transaction->status = 'completed';
            $transaction->save();
            
            DB::commit();
            
            return back()->with('success', 'Withdrawal approved successfully!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to approve withdrawal: ' . $e->getMessage());
        }
    }
    
    /**
     * Reject a pending withdrawal
     */
    public function rejectWithdrawal($id)
    {
        $transaction = Transaction::findOrFail($id);
        
        if ($transaction->status !== 'pending') {
            return back()->with('error', 'This withdrawal has already been processed.');
        }
        
        DB::beginTransaction();
        
        try {
            $transaction->status = 'failed';
            $transaction->save();
            
            // Refund the money back to user's wallet
            $user = $transaction->user;
            $user->wallet_balance = $user->wallet_balance + $transaction->amount;
            $user->save();
            
            DB::commit();
            
            return back()->with('success', 'Withdrawal rejected and funds refunded to user wallet.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to reject withdrawal: ' . $e->getMessage());
        }
    }
    
    /**
     * Mark a pending deposit as completed
     */
    public function approveDeposit($id)
    {
        $payment = Payment::findOrFail($id);
        
        if ($payment->status !== 'pending') {
            return back()->with('error', 'This payment has already been processed.');
        }
        
        DB::beginTransaction();
        
        try {
            $payment->status = 'completed';
            $payment->save();
            
            // Add to user's wallet
            $user = $payment->user;
            $user->wallet_balance = $user->wallet_balance + $payment->amount;
            $user->save();
            
            // Create transaction record
            Transaction::create([
                'user_id' => $user->id,
                'type' => 'credit',
                'amount' => $payment->amount,
                'balance_after' => $user->wallet_balance,
                'description' => 'Deposit via ' . ($payment->method ?? 'bank'),
                'reference' => $payment->reference ?? 'DEP_' . time(),
                'status' => 'completed',
                'method' => $payment->method ?? 'bank',
                'payable_type' => 'App\\Models\\User',
                'payable_id' => $user->id,
            ]);
            
            DB::commit();
            
            return back()->with('success', 'Deposit approved and added to user wallet!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to approve deposit: ' . $e->getMessage());
        }
    }
}