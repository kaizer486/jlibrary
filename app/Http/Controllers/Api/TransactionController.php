<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    /**
     * Get all transactions for the authenticated user
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $query = Transaction::where('user_id', $user->id);
        
        // Filter by type
        if ($request->type && in_array($request->type, ['credit', 'debit', 'withdrawal', 'commission'])) {
            $query->where('type', $request->type);
        }
        
        // Filter by status
        if ($request->status && in_array($request->status, ['pending', 'completed', 'failed', 'processing'])) {
            $query->where('status', $request->status);
        }
        
        // Filter by date range
        if ($request->from_date) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->to_date) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }
        
        // Pagination
        $limit = $request->get('limit', 15);
        $transactions = $query->orderBy('created_at', 'desc')->paginate($limit);
        
        // Calculate totals
        $totalCredits = Transaction::where('user_id', $user->id)
            ->where('type', 'credit')
            ->where('status', 'completed')
            ->sum('amount');
        
        $totalDebits = Transaction::where('user_id', $user->id)
            ->where('type', 'debit')
            ->where('status', 'completed')
            ->sum('amount');
        
        $totalWithdrawals = Transaction::where('user_id', $user->id)
            ->where('type', 'debit')
            ->where('method', 'withdrawal')
            ->where('status', 'completed')
            ->sum('amount');
        
        return response()->json([
            'success' => true,
            'data' => [
                'transactions' => $transactions->items(),
                'summary' => [
                    'total_credits' => (float) $totalCredits,
                    'total_debits' => (float) $totalDebits,
                    'total_withdrawals' => (float) $totalWithdrawals,
                    'current_balance' => (float) $user->wallet_balance,
                ],
                'pagination' => [
                    'current_page' => $transactions->currentPage(),
                    'last_page' => $transactions->lastPage(),
                    'per_page' => $transactions->perPage(),
                    'total' => $transactions->total(),
                ]
            ]
        ]);
    }
    
    /**
     * Get single transaction details
     */
    public function show($id)
    {
        $user = Auth::user();
        
        $transaction = Transaction::where('id', $id)
            ->where('user_id', $user->id)
            ->first();
        
        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $transaction->id,
                'type' => $transaction->type,
                'amount' => (float) $transaction->amount,
                'balance_after' => (float) $transaction->balance_after,
                'description' => $transaction->description,
                'reference' => $transaction->reference,
                'status' => $transaction->status,
                'method' => $transaction->method,
                'created_at' => $transaction->created_at->toISOString(),
                'formatted_date' => $transaction->created_at->format('M d, Y H:i'),
                'formatted_amount' => ($transaction->type === 'credit' ? '+' : '-') . ' TSh ' . number_format($transaction->amount, 2),
            ]
        ]);
    }
    
    /**
     * Get transaction statistics for the authenticated user
     */
    public function stats()
    {
        $user = Auth::user();
        
        // Get last 30 days transactions
        $thirtyDaysAgo = now()->subDays(30);
        
        $recentTransactions = Transaction::where('user_id', $user->id)
            ->where('created_at', '>=', $thirtyDaysAgo)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($transaction) {
                return [
                    'id' => $transaction->id,
                    'type' => $transaction->type,
                    'amount' => (float) $transaction->amount,
                    'description' => $transaction->description,
                    'status' => $transaction->status,
                    'created_at' => $transaction->created_at->diffForHumans(),
                ];
            });
        
        // Monthly spending chart data
        $monthlyData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $spending = Transaction::where('user_id', $user->id)
                ->where('type', 'debit')
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->where('status', 'completed')
                ->sum('amount');
            
            $deposits = Transaction::where('user_id', $user->id)
                ->where('type', 'credit')
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->where('status', 'completed')
                ->sum('amount');
            
            $monthlyData[] = [
                'month' => $month->format('M Y'),
                'spending' => (float) $spending,
                'deposits' => (float) $deposits,
            ];
        }
        
        return response()->json([
            'success' => true,
            'data' => [
                'current_balance' => (float) $user->wallet_balance,
                'recent_transactions' => $recentTransactions,
                'monthly_stats' => $monthlyData,
                'total_transactions' => Transaction::where('user_id', $user->id)->count(),
            ]
        ]);
    }
    
    /**
     * Export transactions as CSV
     */
    public function export(Request $request)
    {
        $user = Auth::user();
        
        $query = Transaction::where('user_id', $user->id);
        
        // Apply filters if provided
        if ($request->from_date) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->to_date) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }
        
        $transactions = $query->orderBy('created_at', 'desc')->get();
        
        $csvFileName = "transactions_{$user->id}_" . date('Y-m-d') . ".csv";
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$csvFileName}",
        ];
        
        $callback = function () use ($transactions) {
            $file = fopen('php://output', 'w');
            
            // Add headers
            fputcsv($file, [
                'Date', 'Type', 'Description', 'Amount', 'Balance After', 'Status', 'Reference', 'Method'
            ]);
            
            // Add rows
            foreach ($transactions as $transaction) {
                fputcsv($file, [
                    $transaction->created_at->format('Y-m-d H:i:s'),
                    $transaction->type,
                    $transaction->description,
                    ($transaction->type === 'credit' ? '+' : '-') . number_format($transaction->amount, 2),
                    number_format($transaction->balance_after, 2),
                    $transaction->status,
                    $transaction->reference,
                    $transaction->method,
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}