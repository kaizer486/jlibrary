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
        
        // Get transaction history
        $transactions = Transaction::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        // Get deposit transactions
        $deposits = Transaction::where('user_id', $user->id)
            ->where('type', 'credit')
            ->where('method', '!=', 'commission')
            ->sum('amount');
        
        // Get purchase transactions
        $purchases = Transaction::where('user_id', $user->id)
            ->where('type', 'debit')
            ->where('method', '!=', 'withdrawal')
            ->sum('amount');
        
        // Get withdrawal transactions
        $withdrawals = Transaction::where('user_id', $user->id)
            ->where('type', 'debit')
            ->where('method', 'withdrawal')
            ->where('status', 'completed')
            ->sum('amount');
        
        // Get pending withdrawals
        $pendingWithdrawals = Transaction::where('user_id', $user->id)
            ->where('type', 'debit')
            ->where('status', 'pending')
            ->sum('amount');
        
        // Calculate available to withdraw
        $availableToWithdraw = $balance - $pendingWithdrawals;
        
        // Get recent activity (last 5 transactions)
        $recentActivity = Transaction::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // Get stats for chart (last 6 months)
        $monthlyStats = $this->getMonthlyStats($user->id);
        
        // Get payment methods for quick actions
        $paymentGateways = config('payments.gateways', []);
        $enabledGateways = [];
        foreach ($paymentGateways as $key => $gateway) {
            if (isset($gateway['enabled']) && $gateway['enabled']) {
                $enabledGateways[$key] = $gateway;
            }
        }
        
        return view('wallet.index', compact(
            'user',
            'balance',
            'transactions',
            'deposits',
            'purchases',
            'withdrawals',
            'pendingWithdrawals',
            'availableToWithdraw',
            'recentActivity',
            'monthlyStats',
            'enabledGateways'
        ));
    }
    
    /**
     * Get monthly transaction stats for chart
     */
    private function getMonthlyStats($userId)
    {
        $months = [];
        $deposits = [];
        $spending = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthName = $month->format('M Y');
            $months[] = $monthName;
            
            // Deposits for this month
            $depositTotal = Transaction::where('user_id', $userId)
                ->where('type', 'credit')
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->sum('amount');
            $deposits[] = (float) $depositTotal;
            
            // Spending for this month
            $spendingTotal = Transaction::where('user_id', $userId)
                ->where('type', 'debit')
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->sum('amount');
            $spending[] = (float) $spendingTotal;
        }
        
        return [
            'months' => $months,
            'deposits' => $deposits,
            'spending' => $spending,
        ];
    }
    
    public function withdraw(Request $request)
    {
        $user = Auth::user();
        $currentBalance = $user->wallet_balance ?? 0;
        $minWithdrawal = config('wallet.withdrawal.min_amount', 5000);
        $maxWithdrawal = config('wallet.withdrawal.max_amount', 10000000);
        
        $request->validate([
            'amount' => 'required|numeric|min:' . $minWithdrawal . '|max:' . min($currentBalance, $maxWithdrawal),
            'payment_method' => 'required|in:mpesa,bank',
            'phone' => 'required_if:payment_method,mpesa|nullable|regex:/^[0-9]{10}$/',
            'account_name' => 'required_if:payment_method,bank|nullable|string|max:255',
            'account_number' => 'required_if:payment_method,bank|nullable|string|max:50',
            'bank_name' => 'required_if:payment_method,bank|nullable|string|max:255',
        ]);
        
        DB::beginTransaction();
        
        try {
            $user = User::where('id', $user->id)->lockForUpdate()->first();
            
            if ($user->wallet_balance < $request->amount) {
                throw new \Exception('Insufficient balance');
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
                'payable_type' => 'App\\Models\\User',
                'payable_id' => $user->id,
            ]);
            
            $user->wallet_balance -= $request->amount;
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
        ]);
        
        return redirect()->route('payment.methods', ['amount' => $request->amount]);
    }
    
    public function getBalance()
    {
        return response()->json([
            'balance' => auth()->user()->wallet_balance,
            'formatted' => 'TSh ' . number_format(auth()->user()->wallet_balance, 2)
        ]);
    }
    
    /**
     * Export transaction history as CSV
     */
    public function exportTransactions(Request $request)
    {
        $user = Auth::user();
        
        $transactions = Transaction::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();
        
        $filename = 'transactions_' . date('Y-m-d') . '.csv';
        
        $handle = fopen('php://temp', 'w');
        fputcsv($handle, ['Date', 'Type', 'Description', 'Amount', 'Balance After', 'Status', 'Reference']);
        
        foreach ($transactions as $transaction) {
            fputcsv($handle, [
                $transaction->created_at->format('Y-m-d H:i:s'),
                $transaction->type,
                $transaction->description,
                ($transaction->type === 'credit' ? '+' : '-') . number_format($transaction->amount, 2),
                number_format($transaction->balance_after, 2),
                $transaction->status,
                $transaction->reference,
            ]);
        }
        
        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);
        
        return response($csv, 200)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }



}