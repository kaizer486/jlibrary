<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Transaction;
use App\Models\User;
use App\Models\WithdrawalRequest; // Add this
use App\Models\InstitutionWallet; // Add this
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
        
        // Get pending withdrawals (User withdrawals - existing)
        $pendingWithdrawals = Transaction::where('type', 'debit')
            ->where('status', 'pending')
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Get pending INSTITUTION withdrawals (NEW)
        $pendingInstitutionWithdrawals = WithdrawalRequest::with(['institution', 'requester'])
            ->where('status', 'pending')
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
        
        // Institution withdrawal stats (NEW)
        $totalInstitutionWithdrawals = WithdrawalRequest::where('status', 'completed')->sum('amount');
        $pendingInstitutionTotal = WithdrawalRequest::where('status', 'pending')->sum('amount');
        
        return view('admin.payments.index', compact(
            'payments',
            'pendingWithdrawals',
            'pendingInstitutionWithdrawals',
            'completedWithdrawals',
            'totalDeposits',
            'pendingDeposits',
            'totalWithdrawals',
            'totalBookSales',
            'totalInstitutionWithdrawals',
            'pendingInstitutionTotal'
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
     * Approve a pending withdrawal (User withdrawal)
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
     * Approve an INSTITUTION withdrawal (NEW)
     */
    public function approveInstitutionWithdrawal($id)
    {
        $withdrawal = WithdrawalRequest::findOrFail($id);
        
        if ($withdrawal->status !== 'pending') {
            return back()->with('error', 'This withdrawal has already been processed.');
        }
        
        DB::beginTransaction();
        
        try {
            $withdrawal->status = 'processing';
            $withdrawal->processed_by = auth()->id();
            $withdrawal->processed_at = now();
            $withdrawal->save();
            
            DB::commit();
            
            return back()->with('success', 'Institution withdrawal marked as processing.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to process withdrawal: ' . $e->getMessage());
        }
    }
    
    /**
     * Complete an INSTITUTION withdrawal (NEW)
     */
    public function completeInstitutionWithdrawal($id)
    {
        $withdrawal = WithdrawalRequest::findOrFail($id);
        
        if ($withdrawal->status !== 'processing') {
            return back()->with('error', 'This withdrawal cannot be completed.');
        }
        
        DB::beginTransaction();
        
        try {
            $withdrawal->status = 'completed';
            $withdrawal->save();
            
            // Update institution wallet
            $wallet = $withdrawal->institution->wallet;
            $wallet->pending_withdrawal -= $withdrawal->amount;
            $wallet->total_withdrawn += $withdrawal->amount;
            $wallet->save();
            
            // Update transaction record
            Transaction::where('reference', 'WD-' . $withdrawal->id)
                ->update(['status' => 'completed']);
            
            DB::commit();
            
            return back()->with('success', 'Institution withdrawal completed successfully!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to complete withdrawal: ' . $e->getMessage());
        }
    }
    
    /**
     * Reject a pending withdrawal (User withdrawal)
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
     * Reject an INSTITUTION withdrawal (NEW)
     */
    public function rejectInstitutionWithdrawal(Request $request, $id)
    {
        $withdrawal = WithdrawalRequest::findOrFail($id);
        
        if ($withdrawal->status !== 'pending') {
            return back()->with('error', 'This withdrawal cannot be rejected.');
        }
        
        $request->validate([
            'rejection_reason' => 'required|string|min:10',
        ]);
        
        DB::beginTransaction();
        
        try {
            $withdrawal->status = 'rejected';
            $withdrawal->processed_by = auth()->id();
            $withdrawal->processed_at = now();
            $withdrawal->rejection_reason = $request->rejection_reason;
            $withdrawal->save();
            
            // Return money to institution wallet
            $wallet = $withdrawal->institution->wallet;
            $wallet->balance += $withdrawal->amount;
            $wallet->pending_withdrawal -= $withdrawal->amount;
            $wallet->save();
            
            // Update transaction record
            Transaction::where('reference', 'WD-' . $withdrawal->id)
                ->update(['status' => 'failed']);
            
            DB::commit();
            
            return back()->with('success', 'Institution withdrawal rejected. Funds returned to wallet.');
            
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