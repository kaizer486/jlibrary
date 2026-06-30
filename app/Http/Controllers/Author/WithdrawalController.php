<?php

namespace App\Http\Controllers\Author;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class WithdrawalController extends Controller
{
    public function index()
    {
        $withdrawals = auth()->user()->transactions()
            ->where('method', 'withdrawal')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('author.withdrawals.index', compact('withdrawals'));
    }

    public function create()
    {
        $user = auth()->user();
        $balance = $user->wallet_balance;

        return view('author.withdrawals.create', compact('balance'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1000|max:' . auth()->user()->wallet_balance,
            'payment_method' => 'required|in:mpesa,tigopesa,halopesa,bank',
            'account_details' => 'required|string|min:5',
        ]);

        $user = auth()->user();

        // Check if user has enough balance
        if ($user->wallet_balance < $request->amount) {
            return back()->with('error', 'Insufficient wallet balance.');
        }

        // Create withdrawal transaction
        $transaction = Transaction::create([
            'user_id' => $user->id,
            'type' => 'debit',
            'method' => 'withdrawal',
            'amount' => $request->amount,
            'balance_after' => $user->wallet_balance - $request->amount,
            'description' => 'Withdrawal request via ' . $request->payment_method,
            'status' => 'pending',
            'metadata' => [
                'payment_method' => $request->payment_method,
                'account_details' => $request->account_details,
            ],
        ]);

        // Deduct from wallet
        $user->wallet_balance -= $request->amount;
        $user->save();

        return redirect()->route('author.withdrawals.index')
            ->with('success', 'Withdrawal request submitted successfully!');
    }
}