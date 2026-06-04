<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WithdrawalRequest;
use App\Models\Transaction;
use Illuminate\Http\Request;

class WithdrawalController extends Controller
{
    public function index(Request $request)
    {
        $query = WithdrawalRequest::with(['institution', 'requester']);
        
        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        
        $withdrawals = $query->latest()->paginate(20);
        
        $stats = [
            'pending' => WithdrawalRequest::where('status', 'pending')->count(),
            'processing' => WithdrawalRequest::where('status', 'processing')->count(),
            'completed' => WithdrawalRequest::where('status', 'completed')->count(),
            'total_amount' => WithdrawalRequest::where('status', 'completed')->sum('amount'),
        ];
        
        return view('admin.withdrawals.index', compact('withdrawals', 'stats'));
    }
    
    public function show(WithdrawalRequest $withdrawal)
    {
        $withdrawal->load(['institution', 'requester', 'processedBy']);
        return view('admin.withdrawals.show', compact('withdrawal'));
    }
    
    public function approve(WithdrawalRequest $withdrawal)
    {
        if ($withdrawal->status !== 'pending') {
            return redirect()->back()->with('error', 'This withdrawal request has already been processed.');
        }
        
        $withdrawal->approve(auth()->id());
        
        return redirect()->back()->with('success', 'Withdrawal request marked as processing.');
    }
    
    public function complete(WithdrawalRequest $withdrawal)
    {
        if ($withdrawal->status !== 'processing') {
            return redirect()->back()->with('error', 'Cannot complete a request that is not in processing state.');
        }
        
        $withdrawal->complete();
        
        // Update transaction status
        Transaction::where('reference', 'WD-' . $withdrawal->id)
            ->update(['status' => 'completed']);
        
        return redirect()->back()->with('success', 'Withdrawal completed successfully!');
    }
    
    public function reject(Request $request, WithdrawalRequest $withdrawal)
    {
        $request->validate([
            'rejection_reason' => 'required|string|min:10',
        ]);
        
        if ($withdrawal->status !== 'pending') {
            return redirect()->back()->with('error', 'Cannot reject a request that is already being processed.');
        }
        
        $withdrawal->reject(auth()->id(), $request->rejection_reason);
        
        // Update transaction status
        Transaction::where('reference', 'WD-' . $withdrawal->id)
            ->update(['status' => 'failed']);
        
        return redirect()->back()->with('success', 'Withdrawal request rejected.');
    }
}