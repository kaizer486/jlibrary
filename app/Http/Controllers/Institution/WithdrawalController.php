<?php

namespace App\Http\Controllers\Institution;

use App\Http\Controllers\Controller;
use App\Models\WithdrawalRequest;
use App\Models\CommissionSetting;
use Illuminate\Http\Request;

class WithdrawalController extends Controller
{
    public function index()
    {
        $institution = auth()->user()->institution;
        $wallet = $institution->wallet;
        
        $withdrawals = WithdrawalRequest::where('institution_id', $institution->id)
            ->latest()
            ->paginate(15);
        
        $minWithdrawal = CommissionSetting::getMinWithdrawal();
        
        return view('institution.withdrawals.index', compact('institution', 'wallet', 'withdrawals', 'minWithdrawal'));
    }
    
    public function create()
    {
        $institution = auth()->user()->institution;
        $wallet = $institution->wallet;
        $minWithdrawal = CommissionSetting::getMinWithdrawal();
        
        if ($wallet->balance < $minWithdrawal) {
            return redirect()->route('institution.withdrawals.index')
                ->with('error', "Minimum withdrawal amount is TSh " . number_format($minWithdrawal, 2));
        }
        
        return view('institution.withdrawals.create', compact('institution', 'wallet', 'minWithdrawal'));
    }
    
    public function store(Request $request)
    {
        $institution = auth()->user()->institution;
        $wallet = $institution->wallet;
        $minWithdrawal = CommissionSetting::getMinWithdrawal();
        
        $request->validate([
            'amount' => 'required|numeric|min:' . $minWithdrawal . '|max:' . $wallet->balance,
            'payment_method' => 'required|in:bank,mpesa,tigopesa,halopesa',
            'account_details' => 'required|string',
            'notes' => 'nullable|string',
        ]);
        
        $withdrawal = WithdrawalRequest::create([
            'institution_id' => $institution->id,
            'requested_by' => auth()->id(),
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'account_details' => $request->account_details,
            'notes' => $request->notes,
            'status' => 'pending',
        ]);
        
        // Deduct from wallet and mark as pending withdrawal
        $wallet->balance -= $request->amount;
        $wallet->pending_withdrawal += $request->amount;
        $wallet->save();
        
        // Create transaction record
        \App\Models\Transaction::create([
            'user_id' => auth()->id(),
            'type' => 'withdrawal',
            'amount' => $request->amount,
            'balance_after' => $wallet->balance,
            'description' => 'Withdrawal request for ' . $institution->name,
            'reference' => 'WD-' . $withdrawal->id,
            'status' => 'pending',
            'method' => $request->payment_method,
        ]);
        
        return redirect()->route('institution.withdrawals.index')
            ->with('success', 'Withdrawal request submitted successfully! Awaiting approval.');
    }
    
    public function show(WithdrawalRequest $withdrawal)
    {
        $institution = auth()->user()->institution;
        
        if ($withdrawal->institution_id !== $institution->id) {
            abort(403);
        }
        
        return view('institution.withdrawals.show', compact('withdrawal', 'institution'));
    }
    
    public function cancel(WithdrawalRequest $withdrawal)
    {
        $institution = auth()->user()->institution;
        
        if ($withdrawal->institution_id !== $institution->id) {
            abort(403);
        }
        
        if ($withdrawal->status !== 'pending') {
            return redirect()->back()->with('error', 'Cannot cancel a withdrawal that is already being processed.');
        }
        
        // Return money to wallet
        $wallet = $institution->wallet;
        $wallet->balance += $withdrawal->amount;
        $wallet->pending_withdrawal -= $withdrawal->amount;
        $wallet->save();
        
        $withdrawal->status = 'cancelled';
        $withdrawal->save();
        
        return redirect()->route('institution.withdrawals.index')
            ->with('success', 'Withdrawal request cancelled.');
    }
}