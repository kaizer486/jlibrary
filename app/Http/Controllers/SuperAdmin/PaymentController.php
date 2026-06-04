<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Transaction;
use App\Models\WithdrawalRequest;
use App\Models\User;
use App\Models\Institution;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with('user');
        
        // Search by user
        if ($request->search) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('full_name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }
        
        // Filter by status
        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        
        // Filter by type
        if ($request->type && $request->type !== 'all') {
            if ($request->type === 'book') {
                $query->where('payable_type', 'App\\Models\\Book');
            } elseif ($request->type === 'deposit') {
                $query->where('payable_type', 'App\\Models\\User');
            }
        }
        
        $payments = $query->latest()->paginate(20);
        
        // Stats
        $totalRevenue = Payment::where('status', 'completed')->sum('amount');
        $totalBookSales = Payment::where('status', 'completed')->where('payable_type', 'App\\Models\\Book')->sum('amount');
        $totalDeposits = Payment::where('status', 'completed')->where('payable_type', 'App\\Models\\User')->sum('amount');
        $pendingPayments = Payment::where('status', 'pending')->sum('amount');
        
        // Withdrawal stats
        $totalWithdrawals = WithdrawalRequest::where('status', 'completed')->sum('amount');
        $pendingWithdrawals = WithdrawalRequest::where('status', 'pending')->sum('amount');
        
        return view('super-admin.payments.index', compact(
            'payments', 'totalRevenue', 'totalBookSales', 'totalDeposits',
            'pendingPayments', 'totalWithdrawals', 'pendingWithdrawals'
        ));
    }
    
    public function show(Payment $payment)
    {
        $payment->load('user');
        return view('super-admin.payments.show', compact('payment'));
    }
    
    public function withdrawalShow(WithdrawalRequest $withdrawal)
{
    $withdrawal->load(['user', 'institution', 'requester', 'processedBy']);
    return view('super-admin.payments.withdrawal-show', compact('withdrawal'));
}
    public function transactions(Request $request)
    {
        $query = Transaction::with('user');
        
        // Search
        if ($request->search) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('full_name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }
        
        // Filter by type
        if ($request->type && $request->type !== 'all') {
            $query->where('type', $request->type);
        }
        
        $transactions = $query->latest()->paginate(20);
        
        $totalCredits = Transaction::where('type', 'credit')->where('status', 'completed')->sum('amount');
        $totalDebits = Transaction::where('type', 'debit')->where('status', 'completed')->sum('amount');
        
        return view('super-admin.payments.transactions', compact('transactions', 'totalCredits', 'totalDebits'));
    }
    
    public function withdrawals(Request $request)
    {
        $query = WithdrawalRequest::with(['user', 'institution', 'requester']);
        
        // Filter by status
        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        
        // Filter by type
        if ($request->type && $request->type !== 'all') {
            if ($request->type === 'user') {
                $query->whereNotNull('user_id');
            } elseif ($request->type === 'institution') {
                $query->whereNotNull('institution_id');
            }
        }
        
        $withdrawals = $query->latest()->paginate(20);
        
        $stats = [
            'pending' => WithdrawalRequest::where('status', 'pending')->sum('amount'),
            'processing' => WithdrawalRequest::where('status', 'processing')->sum('amount'),
            'completed' => WithdrawalRequest::where('status', 'completed')->sum('amount'),
            'total' => WithdrawalRequest::sum('amount'),
        ];
        
        return view('super-admin.payments.withdrawals', compact('withdrawals', 'stats'));
    }
}