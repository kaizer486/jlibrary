<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    public function balance()
    {
        $user = Auth::user();
        
        return response()->json([
            'success' => true,
            'balance' => $user->wallet_balance,
            'formatted' => 'TSh ' . number_format($user->wallet_balance, 2)
        ]);
    }
    
    public function withdraw(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:5000',
            'payment_method' => 'required|in:mpesa,bank',
            'phone' => 'required_if:payment_method,mpesa',
        ]);
        
        $user = Auth::user();
        
        if ($user->wallet_balance < $request->amount) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient balance'
            ], 400);
        }
        
        $transaction = Transaction::create([
            'user_id' => $user->id,
            'type' => 'debit',
            'amount' => $request->amount,
            'balance_after' => $user->wallet_balance - $request->amount,
            'description' => 'Withdrawal request via ' . $request->payment_method,
            'reference' => 'WTD_' . uniqid() . '_' . $user->id,
            'status' => 'pending',
            'method' => 'withdrawal',
        ]);
        
        $user->wallet_balance -= $request->amount;
        $user->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Withdrawal request submitted',
            'transaction_id' => $transaction->id
        ]);
    }
}