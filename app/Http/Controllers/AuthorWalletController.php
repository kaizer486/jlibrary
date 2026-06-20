<?php

namespace App\Http\Controllers;

use App\Models\AuthorWallet;
use App\Models\CommissionLog;
use App\Models\Transaction;
use App\Models\User;
use App\Services\CommissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuthorWalletController extends Controller
{
    protected $commissionService;
    
    public function __construct(CommissionService $commissionService)
    {
        $this->commissionService = $commissionService;
    }
    
    /**
     * Show author wallet dashboard
     */
    public function index()
    {
        $user = Auth::user();
        
        // Check if user is an author
        if (!$user->isAuthor() && !$user->isApprovedAuthor()) {
            return redirect()->route('dashboard')
                ->with('error', 'You are not authorized to access the author dashboard.');
        }
        
        // Get or create author wallet
        $wallet = AuthorWallet::firstOrCreate(
            ['user_id' => $user->id],
            ['currency' => 'TZS']
        );
        
        // Get commission history
        $commissions = CommissionLog::where('author_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        // Get stats
        $totalEarned = $this->commissionService->getAuthorTotalEarnings($user->id);
        $pendingWithdrawals = $wallet->pending_withdrawal;
        $availableBalance = $wallet->balance;
        
        // Get withdrawal history
        $withdrawals = Transaction::where('user_id', $user->id)
            ->where('type', 'debit')
            ->where('method', 'author_withdrawal')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        return view('author.wallet.index', compact(
            'wallet', 'commissions', 'totalEarned', 
            'pendingWithdrawals', 'availableBalance', 'withdrawals'
        ));
    }
    
    /**
     * Request withdrawal from author wallet
     */
    public function requestWithdrawal(Request $request)
    {
        $user = Auth::user();
        $wallet = AuthorWallet::where('user_id', $user->id)->first();
        
        if (!$wallet || $wallet->balance <= 0) {
            return back()->with('error', 'No funds available for withdrawal.');
        }
        
        $request->validate([
            'amount' => 'required|numeric|min:5000|max:' . $wallet->balance,
            'payment_method' => 'required|in:mpesa,bank',
            'phone' => 'required_if:payment_method,mpesa|nullable|regex:/^[0-9]{10}$/',
            'account_name' => 'required_if:payment_method,bank|nullable|string',
            'account_number' => 'required_if:payment_method,bank|nullable|string',
        ]);
        
        DB::beginTransaction();
        
        try {
            $lockedWallet = AuthorWallet::where('id', $wallet->id)->lockForUpdate()->first();
            
            if ($lockedWallet->balance < $request->amount) {
                throw new \Exception('Insufficient balance');
            }
            
            // Mark withdrawal request
            $lockedWallet->markWithdrawalRequest($request->amount);
            
            // Create transaction record
            Transaction::create([
                'user_id' => $user->id,
                'type' => 'debit',
                'amount' => $request->amount,
                'balance_after' => $lockedWallet->balance,
                'description' => 'Author withdrawal request via ' . $request->payment_method,
                'reference' => 'AUTH_WD_' . uniqid() . '_' . $user->id,
                'status' => 'pending',
                'method' => 'author_withdrawal',
            ]);
            
            DB::commit();
            
            return back()->with('success', 'Withdrawal request submitted. Admin will process within 48 hours.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }
}