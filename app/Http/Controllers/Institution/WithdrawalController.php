<?php

namespace App\Http\Controllers\Institution;

use App\Http\Controllers\Controller;
use App\Models\WithdrawalRequest;
use App\Models\Transaction;
use App\Models\CommissionSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; 

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
    
    DB::beginTransaction();
    
    try {
        $withdrawal = WithdrawalRequest::create([
            'institution_id' => $institution->id,
            'requested_by' => auth()->id(),
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'account_details' => encrypt($request->account_details),  // ✅ Encrypt!
            'notes' => $request->notes,
            'status' => 'pending',
        ]);
        
        // ✅ REMOVED immediate balance deduction
        // ✅ Only mark pending withdrawal amount
        $wallet->pending_withdrawal += $request->amount;
        $wallet->save();
        
        Transaction::create([
            'user_id' => auth()->id(),
            'type' => 'withdrawal',
            'amount' => $request->amount,
            'balance_after' => $wallet->balance,  // Still has full balance
            'description' => 'Withdrawal request for ' . $institution->name,
            'reference' => 'WD-' . $withdrawal->id,
            'status' => 'pending',
            'method' => $request->payment_method,
        ]);
        
        DB::commit();
        
        return redirect()->route('institution.withdrawals.index')
            ->with('success', 'Withdrawal request submitted successfully! Awaiting approval.');
            
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Failed to submit withdrawal request: ' . $e->getMessage());
    }
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