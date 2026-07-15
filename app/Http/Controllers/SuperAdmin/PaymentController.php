<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Transaction;
use App\Models\WithdrawalRequest;
use App\Models\CommissionLog;
use App\Models\AuthorWallet;
use App\Models\PaymentAuditLog;
use App\Models\User;
use App\Models\Institution;
use App\Services\CommissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PaymentController extends Controller
{
    protected $commissionService;
    
    public function __construct(CommissionService $commissionService)
    {
        $this->commissionService = $commissionService;
    }
    
    /**
     * Main payment dashboard with all stats
     */
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
        
        // Commission stats (80/20)
        $totalPlatformFees = $this->commissionService->getPlatformTotalFees();
        $totalAuthorEarnings = CommissionLog::where('status', 'completed')->sum('author_earnings');
        
        // Author withdrawal stats
        $pendingAuthorPayouts = Transaction::where('type', 'debit')
            ->where('method', 'author_withdrawal')
            ->where('status', 'pending')
            ->sum('amount');
        
        $completedAuthorPayouts = Transaction::where('type', 'debit')
            ->where('method', 'author_withdrawal')
            ->where('status', 'completed')
            ->sum('amount');
        
        // ✅ FIXED: Define totalTransactions
        $totalTransactions = CommissionLog::count();
        
        // Monthly revenue chart data
        $monthlyRevenue = $this->getMonthlyRevenue();
        
        return view('super-admin.payments.index', compact(
            'payments', 'totalRevenue', 'totalBookSales', 'totalDeposits',
            'pendingPayments', 'totalWithdrawals', 'pendingWithdrawals',
            'totalPlatformFees', 'totalAuthorEarnings', 'pendingAuthorPayouts',
            'completedAuthorPayouts', 'monthlyRevenue', 'totalTransactions'
        ));
    }
    
    /**
     * Get monthly revenue for chart
     */
    private function getMonthlyRevenue()
    {
        $months = [];
        $revenue = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $months[] = $month->format('M Y');
            
            $monthlyTotal = Payment::where('status', 'completed')
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->sum('amount');
            
            $revenue[] = (float) $monthlyTotal;
        }
        
        return ['months' => $months, 'revenue' => $revenue];
    }
    
    /**
     * Approve a pending user withdrawal (SuperAdmin only)
     */
    public function approveUserWithdrawal($id)
    {
        $transaction = Transaction::findOrFail($id);
        
        if ($transaction->status !== 'pending') {
            return back()->with('error', 'This withdrawal has already been processed.');
        }
        
        DB::beginTransaction();
        
        try {
            $transaction->status = 'completed';
            $transaction->save();
            
            PaymentAuditLog::create([
                'admin_id' => auth()->id(),
                'auditable_type' => Transaction::class,
                'auditable_id' => $transaction->id,
                'action' => 'approve_user_withdrawal',
                'amount' => $transaction->amount,
                'ip_address' => request()->ip(),
            ]);
            
            DB::commit();
            
            return back()->with('success', 'User withdrawal approved successfully!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to approve withdrawal: ' . $e->getMessage());
        }
    }

    /**
     * Reject a pending user withdrawal (SuperAdmin only)
     */
    public function rejectUserWithdrawal(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|min:10',
        ]);
        
        $transaction = Transaction::findOrFail($id);
        
        if ($transaction->status !== 'pending') {
            return back()->with('error', 'This withdrawal has already been processed.');
        }
        
        DB::beginTransaction();
        
        try {
            $transaction->status = 'failed';
            $transaction->notes = $request->rejection_reason;
            $transaction->save();
            
            // Refund the money back to user's wallet
            $user = $transaction->user;
            $user->wallet_balance = $user->wallet_balance + $transaction->amount;
            $user->save();
            
            PaymentAuditLog::create([
                'admin_id' => auth()->id(),
                'auditable_type' => Transaction::class,
                'auditable_id' => $transaction->id,
                'action' => 'reject_user_withdrawal',
                'reason' => $request->rejection_reason,
                'amount' => $transaction->amount,
                'ip_address' => request()->ip(),
            ]);
            
            DB::commit();
            
            return back()->with('success', 'Withdrawal rejected and funds refunded to user wallet.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to reject withdrawal: ' . $e->getMessage());
        }
    }

    /**
     * Approve institution withdrawal (SuperAdmin only)
     */
    public function approveInstitutionWithdrawal($id)
    {
        $withdrawal = WithdrawalRequest::findOrFail($id);
        
        if ($withdrawal->status !== 'pending') {
            return back()->with('error', 'This withdrawal has already been processed.');
        }
        
        DB::beginTransaction();
        
        try {
            $withdrawal->status = 'processing';
            $withdrawal->processed_by = auth()->id();
            $withdrawal->processed_at = now();
            $withdrawal->save();
            
            PaymentAuditLog::create([
                'admin_id' => auth()->id(),
                'auditable_type' => WithdrawalRequest::class,
                'auditable_id' => $withdrawal->id,
                'action' => 'approve_institution_withdrawal',
                'amount' => $withdrawal->amount,
                'ip_address' => request()->ip(),
            ]);
            
            DB::commit();
            
            return back()->with('success', 'Institution withdrawal marked as processing.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to process withdrawal: ' . $e->getMessage());
        }
    }

    /**
     * Complete institution withdrawal (SuperAdmin only)
     */
    public function completeInstitutionWithdrawal($id)
    {
        $withdrawal = WithdrawalRequest::findOrFail($id);
        
        if ($withdrawal->status !== 'processing') {
            return back()->with('error', 'This withdrawal cannot be completed.');
        }
        
        DB::beginTransaction();
        
        try {
            $withdrawal->status = 'completed';
            $withdrawal->save();
            
            // Update institution wallet
            $wallet = $withdrawal->institution->wallet;
            $wallet->pending_withdrawal -= $withdrawal->amount;
            $wallet->total_withdrawn += $withdrawal->amount;
            $wallet->save();
            
            // Update transaction record
            Transaction::where('reference', 'WD-' . $withdrawal->id)
                ->update(['status' => 'completed']);
            
            PaymentAuditLog::create([
                'admin_id' => auth()->id(),
                'auditable_type' => WithdrawalRequest::class,
                'auditable_id' => $withdrawal->id,
                'action' => 'complete_institution_withdrawal',
                'amount' => $withdrawal->amount,
                'ip_address' => request()->ip(),
            ]);
            
            DB::commit();
            
            return back()->with('success', 'Institution withdrawal completed successfully!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to complete withdrawal: ' . $e->getMessage());
        }
    }

    /**
     * Reject institution withdrawal (SuperAdmin only)
     */
    public function rejectInstitutionWithdrawal(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|min:10',
        ]);
        
        $withdrawal = WithdrawalRequest::findOrFail($id);
        
        if ($withdrawal->status !== 'pending') {
            return back()->with('error', 'This withdrawal cannot be rejected.');
        }
        
        DB::beginTransaction();
        
        try {
            $withdrawal->status = 'rejected';
            $withdrawal->processed_by = auth()->id();
            $withdrawal->processed_at = now();
            $withdrawal->rejection_reason = $request->rejection_reason;
            $withdrawal->save();
            
            // Return money to institution wallet
            $wallet = $withdrawal->institution->wallet;
            $wallet->balance += $withdrawal->amount;
            $wallet->pending_withdrawal -= $withdrawal->amount;
            $wallet->save();
            
            // Update transaction record
            Transaction::where('reference', 'WD-' . $withdrawal->id)
                ->update(['status' => 'failed']);
            
            PaymentAuditLog::create([
                'admin_id' => auth()->id(),
                'auditable_type' => WithdrawalRequest::class,
                'auditable_id' => $withdrawal->id,
                'action' => 'reject_institution_withdrawal',
                'reason' => $request->rejection_reason,
                'amount' => $withdrawal->amount,
                'ip_address' => request()->ip(),
            ]);
            
            DB::commit();
            
            return back()->with('success', 'Institution withdrawal rejected. Funds returned to wallet.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to reject withdrawal: ' . $e->getMessage());
        }
    }

    /**
     * Approve author payout (SuperAdmin only)
     */
    public function approveAuthorPayout($transactionId)
    {
        $transaction = Transaction::findOrFail($transactionId);
        
        if ($transaction->status !== 'pending') {
            return back()->with('error', 'This payout has already been processed.');
        }
        
        DB::beginTransaction();
        
        try {
            $authorWallet = AuthorWallet::where('user_id', $transaction->user_id)->first();
            
            if ($authorWallet) {
                $authorWallet->completeWithdrawal($transaction->amount);
            }
            
            $transaction->status = 'completed';
            $transaction->save();
            
            PaymentAuditLog::create([
                'admin_id' => auth()->id(),
                'auditable_type' => Transaction::class,
                'auditable_id' => $transaction->id,
                'action' => 'approve_author_payout',
                'amount' => $transaction->amount,
                'ip_address' => request()->ip(),
            ]);
            
            DB::commit();
            
            return back()->with('success', 'Author payout approved successfully!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to approve payout: ' . $e->getMessage());
        }
    }

    /**
     * Reject author payout (SuperAdmin only)
     */
    public function rejectAuthorPayout(Request $request, $transactionId)
    {
        $request->validate([
            'reason' => 'required|string|min:10',
        ]);
        
        $transaction = Transaction::findOrFail($transactionId);
        
        if ($transaction->status !== 'pending') {
            return back()->with('error', 'This payout has already been processed.');
        }
        
        DB::beginTransaction();
        
        try {
            $authorWallet = AuthorWallet::where('user_id', $transaction->user_id)->first();
            
            if ($authorWallet) {
                $authorWallet->cancelWithdrawal($transaction->amount);
            }
            
            $transaction->status = 'failed';
            $transaction->notes = $request->reason;
            $transaction->save();
            
            PaymentAuditLog::create([
                'admin_id' => auth()->id(),
                'auditable_type' => Transaction::class,
                'auditable_id' => $transaction->id,
                'action' => 'reject_author_payout',
                'reason' => $request->reason,
                'amount' => $transaction->amount,
                'ip_address' => request()->ip(),
            ]);
            
            DB::commit();
            
            return back()->with('success', 'Author payout rejected. Funds returned to author wallet.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to reject payout: ' . $e->getMessage());
        }
    }
    
    /**
     * Show single payment details
     */
    public function show(Payment $payment)
    {
        $payment->load('user');
        return view('super-admin.payments.show', compact('payment'));
    }
    
    /**
     * Show withdrawal request details
     */
    public function withdrawalShow(WithdrawalRequest $withdrawal)
    {
        $withdrawal->load(['user', 'institution', 'requester', 'processedBy']);
        return view('super-admin.payments.withdrawal-show', compact('withdrawal'));
    }
    
    /**
     * All transactions view
     */
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
        
        // Filter by status
        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        
        // Filter by date range
        if ($request->from_date) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->to_date) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }
        
        $transactions = $query->latest()->paginate(20);
        
        $totalCredits = Transaction::where('type', 'credit')->where('status', 'completed')->sum('amount');
        $totalDebits = Transaction::where('type', 'debit')->where('status', 'completed')->sum('amount');
        
        return view('super-admin.payments.transactions', compact('transactions', 'totalCredits', 'totalDebits'));
    }
    
    /**
     * Delete a transaction permanently (SuperAdmin only)
     */
    public function deleteTransaction($id)
    {
        $transaction = Transaction::findOrFail($id);
        
        DB::beginTransaction();
        
        try {
            // Log the deletion
            PaymentAuditLog::create([
                'admin_id' => auth()->id(),
                'auditable_type' => Transaction::class,
                'auditable_id' => $transaction->id,
                'action' => 'delete_transaction',
                'amount' => $transaction->amount,
                'ip_address' => request()->ip(),
                'metadata' => json_encode([
                    'transaction_data' => $transaction->toArray(),
                    'deleted_by' => auth()->user()->email,
                    'status_before_delete' => $transaction->status,
                ]),
            ]);
            
            // Hard delete the transaction
            $transaction->forceDelete();
            
            DB::commit();
            
            return redirect()->back()->with('success', 'Transaction deleted successfully!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete transaction: ' . $e->getMessage());
        }
    }

    /**
     * Bulk delete transactions permanently
     */
    public function bulkDeleteTransactions(Request $request)
    {
        $request->validate([
            'transaction_ids' => 'required|array',
            'transaction_ids.*' => 'exists:transactions,id',
        ]);
        
        $deletedCount = 0;
        $failedCount = 0;
        
        DB::beginTransaction();
        
        try {
            foreach ($request->transaction_ids as $transactionId) {
                $transaction = Transaction::find($transactionId);
                
                if (!$transaction) {
                    $failedCount++;
                    continue;
                }
                
                // Log each deletion
                PaymentAuditLog::create([
                    'admin_id' => auth()->id(),
                    'auditable_type' => Transaction::class,
                    'auditable_id' => $transaction->id,
                    'action' => 'bulk_delete_transaction',
                    'amount' => $transaction->amount,
                    'ip_address' => request()->ip(),
                    'metadata' => json_encode([
                        'bulk_deletion' => true,
                        'deleted_by' => auth()->user()->email,
                        'status_before_delete' => $transaction->status,
                    ]),
                ]);
                
                $transaction->forceDelete();
                $deletedCount++;
            }
            
            DB::commit();
            
            return redirect()->back()->with('success', "Deleted {$deletedCount} transactions. Failed: {$failedCount}");
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete transactions: ' . $e->getMessage());
        }
    }

    /**
     * Withdrawals view (user + institution)
     */
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
    
    /**
     * Commission Reports
     */
    public function commissions(Request $request)
    {
        $query = CommissionLog::with(['author', 'buyer']);
        
        // Filter by author
        if ($request->author_id) {
            $query->where('author_id', $request->author_id);
        }
        
        // Filter by date range
        if ($request->from_date) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->to_date) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }
        
        $commissions = $query->latest()->paginate(20);
        
        $totalAuthorEarnings = CommissionLog::where('status', 'completed')->sum('author_earnings');
        $totalPlatformFees = CommissionLog::where('status', 'completed')->sum('platform_fee');
        $totalTransactions = CommissionLog::count();
        
        $topAuthors = CommissionLog::select('author_id')
            ->selectRaw('SUM(author_earnings) as total_earnings')
            ->where('status', 'completed')
            ->groupBy('author_id')
            ->with('author')
            ->orderBy('total_earnings', 'desc')
            ->limit(10)
            ->get();
        
        return view('super-admin.payments.commissions', compact(
            'commissions', 'totalAuthorEarnings', 'totalPlatformFees', 
            'totalTransactions', 'topAuthors'
        ));
    }
    
    /**
     * Author Payouts Management
     */
    public function authorPayouts(Request $request)
    {
        $query = Transaction::where('type', 'debit')
            ->where('method', 'author_withdrawal')
            ->with('user');
        
        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        
        $payouts = $query->latest()->paginate(20);
        
        $stats = [
            'pending' => Transaction::where('type', 'debit')->where('method', 'author_withdrawal')->where('status', 'pending')->sum('amount'),
            'completed' => Transaction::where('type', 'debit')->where('method', 'author_withdrawal')->where('status', 'completed')->sum('amount'),
            'total' => Transaction::where('type', 'debit')->where('method', 'author_withdrawal')->sum('amount'),
        ];
        
        return view('super-admin.payments.author-payouts', compact('payouts', 'stats'));
    }
    
    /**
     * Payment Audit Logs
     */
    public function auditLogs(Request $request)
    {
        $query = PaymentAuditLog::with('admin');
        
        if ($request->action && $request->action !== 'all') {
            $query->where('action', $request->action);
        }
        
        $logs = $query->latest()->paginate(30);
        
        return view('super-admin.payments.audit-logs', compact('logs'));
    }
    
    /**
     * Export Financial Report
     */
    public function exportReport(Request $request)
    {
        $fromDate = $request->from_date ?? Carbon::now()->startOfMonth();
        $toDate = $request->to_date ?? Carbon::now();
        
        $payments = Payment::whereBetween('created_at', [$fromDate, $toDate])
            ->where('status', 'completed')
            ->get();
        
        $filename = 'financial_report_' . date('Y-m-d') . '.csv';
        
        $handle = fopen('php://temp', 'w');
        fputcsv($handle, ['Date', 'User', 'Type', 'Amount', 'Method', 'Reference']);
        
        foreach ($payments as $payment) {
            fputcsv($handle, [
                $payment->created_at->format('Y-m-d H:i:s'),
                $payment->user->email ?? 'N/A',
                $payment->payable_type === 'App\\Models\\Book' ? 'Book Sale' : 'Deposit',
                $payment->amount,
                $payment->method,
                $payment->reference,
            ]);
        }
        
        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);
        
        return response($csv, 200)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
    public function deletePayment($id)
{
    $payment = Payment::findOrFail($id);

    DB::beginTransaction();

    try {
        PaymentAuditLog::create([
            'admin_id' => auth()->id(),
            'auditable_type' => Payment::class,
            'auditable_id' => $payment->id,
            'action' => 'delete_payment',
            'amount' => $payment->amount,
            'ip_address' => request()->ip(),
            'metadata' => json_encode([
                'payment_data' => $payment->toArray(),
                'deleted_by' => auth()->user()->email,
                'status_before_delete' => $payment->status,
            ]),
        ]);

        $payment->delete(); // or forceDelete() if Payment doesn't use SoftDeletes

        DB::commit();

        return redirect()->back()->with('success', 'Payment deleted successfully!');

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Failed to delete payment: ' . $e->getMessage());
    }
}
}