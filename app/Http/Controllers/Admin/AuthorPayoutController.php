<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuthorWallet;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuthorPayoutController extends Controller
{
    /**
     * Show pending author withdrawals
     */
    public function index()
    {
        $pendingWithdrawals = Transaction::where('type', 'debit')
            ->where('method', 'author_withdrawal')
            ->where('status', 'pending')
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        $completedWithdrawals = Transaction::where('type', 'debit')
            ->where('method', 'author_withdrawal')
            ->where('status', 'completed')
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();
        
        $totalPending = Transaction::where('type', 'debit')
            ->where('method', 'author_withdrawal')
            ->where('status', 'pending')
            ->sum('amount');
        
        $totalPaid = Transaction::where('type', 'debit')
            ->where('method', 'author_withdrawal')
            ->where('status', 'completed')
            ->sum('amount');
        
        return view('admin.author-payouts.index', compact(
            'pendingWithdrawals', 'completedWithdrawals', 'totalPending', 'totalPaid'
        ));
    }
    
    /**
     * Approve author withdrawal
     */
    public function approve($transactionId)
    {
        $transaction = Transaction::findOrFail($transactionId);
        
        if ($transaction->status !== 'pending') {
            return back()->with('error', 'This withdrawal has already been processed.');
        }
        
        DB::beginTransaction();
        
        try {
            $authorWallet = AuthorWallet::where('user_id', $transaction->user_id)->first();
            
            if (!$authorWallet) {
                throw new \Exception('Author wallet not found');
            }
            
            // Complete withdrawal in author wallet
            $authorWallet->completeWithdrawal($transaction->amount);
            
            // Update transaction
            $transaction->status = 'completed';
            $transaction->save();
            
            DB::commit();
            
            // TODO: Send SMS/Email notification to author
            // TODO: Initiate actual money transfer via API (M-Pesa/Bank)
            
            return back()->with('success', 'Author withdrawal approved and marked as paid.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to approve withdrawal: ' . $e->getMessage());
        }
    }
    
    /**
     * Reject author withdrawal
     */
    public function reject(Request $request, $transactionId)
    {
        $request->validate([
            'reason' => 'required|string|min:10',
        ]);
        
        $transaction = Transaction::findOrFail($transactionId);
        
        if ($transaction->status !== 'pending') {
            return back()->with('error', 'This withdrawal has already been processed.');
        }
        
        DB::beginTransaction();
        
        try {
            $authorWallet = AuthorWallet::where('user_id', $transaction->user_id)->first();
            
            if ($authorWallet) {
                // Refund back to author wallet
                $authorWallet->cancelWithdrawal($transaction->amount);
            }
            
            $transaction->status = 'failed';
            $transaction->notes = $request->reason;
            $transaction->save();
            
            DB::commit();
            
            return back()->with('success', 'Author withdrawal rejected. Funds returned to author wallet.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to reject withdrawal: ' . $e->getMessage());
        }
    }
}