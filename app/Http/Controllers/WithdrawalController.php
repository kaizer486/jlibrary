<?php

namespace App\Http\Controllers;

use App\Models\WithdrawalRequest;
use App\Models\Transaction;
use App\Models\CommissionSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WithdrawalController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $minWithdrawal = CommissionSetting::getMinWithdrawal();
        
        $withdrawals = WithdrawalRequest::where('user_id', $user->id)
            ->orWhere('requested_by', $user->id)
            ->whereNotNull('user_id')
            ->latest()
            ->paginate(15);
        
        return view('withdrawals.index', compact('user', 'minWithdrawal', 'withdrawals'));
    }
    
    public function create()
    {
        $user = auth()->user();
        $minWithdrawal = CommissionSetting::getMinWithdrawal();
        
        if ($user->wallet_balance < $minWithdrawal) {
            return redirect()->route('withdrawals.index')
                ->with('error', "Minimum withdrawal amount is TSh " . number_format($minWithdrawal, 2) . ". Your balance is TSh " . number_format($user->wallet_balance, 2));
        }
        
        return view('withdrawals.create', compact('user', 'minWithdrawal'));
    }
    
    public function store(Request $request)
    {
        $user = auth()->user();
        $minWithdrawal = CommissionSetting::getMinWithdrawal();
        
        $request->validate([
            'amount' => 'required|numeric|min:' . $minWithdrawal . '|max:' . $user->wallet_balance,
            'payment_method' => 'required|in:bank,mpesa,tigopesa,halopesa',
            'account_details' => 'required|string',
            'notes' => 'nullable|string',
        ]);
        
        DB::beginTransaction();
        
        try {
            // Create withdrawal request
            $withdrawal = WithdrawalRequest::create([
                'user_id' => $user->id,
                'requested_by' => $user->id,
                'amount' => $request->amount,
                'payment_method' => $request->payment_method,
                'account_details' => $request->account_details,
                'notes' => $request->notes,
                'status' => 'pending',
                'type' => 'user',
            ]);
            
            // Deduct from user's wallet
            $oldBalance = $user->wallet_balance;
            $user->wallet_balance -= $request->amount;
            $user->save();
            
            // Create transaction record
            Transaction::create([
                'user_id' => $user->id,
                'type' => 'debit',
                'amount' => $request->amount,
                'balance_after' => $user->wallet_balance,
                'description' => 'Withdrawal request to ' . strtoupper($request->payment_method),
                'reference' => 'WD-' . $withdrawal->id,
                'status' => 'pending',
                'method' => $request->payment_method,
            ]);
            
            DB::commit();
            
            return redirect()->route('withdrawals.index')
                ->with('success', 'Withdrawal request submitted successfully! Awaiting approval.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to submit withdrawal request: ' . $e->getMessage());
        }
    }
    
    public function show(WithdrawalRequest $withdrawal)
    {
        if ($withdrawal->user_id !== auth()->id() && $withdrawal->requested_by !== auth()->id()) {
            abort(403);
        }
        
        return view('withdrawals.show', compact('withdrawal'));
    }
    
    public function cancel(WithdrawalRequest $withdrawal)
    {
        if ($withdrawal->user_id !== auth()->id()) {
            abort(403);
        }
        
        if ($withdrawal->status !== 'pending') {
            return redirect()->back()->with('error', 'Cannot cancel a withdrawal that is already being processed.');
        }
        
        DB::beginTransaction();
        
        try {
            // Refund money to user's wallet
            $user = $withdrawal->user;
            $user->wallet_balance += $withdrawal->amount;
            $user->save();
            
            $withdrawal->status = 'cancelled';
            $withdrawal->save();
            
            // Update transaction record
            Transaction::where('reference', 'WD-' . $withdrawal->id)
                ->update(['status' => 'cancelled']);
            
            DB::commit();
            
            return redirect()->route('withdrawals.index')
                ->with('success', 'Withdrawal request cancelled. Funds returned to your wallet.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to cancel withdrawal: ' . $e->getMessage());
        }
    }
}