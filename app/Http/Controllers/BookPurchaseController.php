<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookshopBook;
use App\Models\Payment;
use App\Models\Transaction;
use App\Models\User;
use App\Services\CommissionService;
use App\Services\PesapalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BookPurchaseController extends Controller
{
    public function __construct(
        protected CommissionService $commissionService,
        protected PesapalService $pesapal
    ) {}

    private function findBook($bookId)
    {
        return Book::find($bookId) ?? BookshopBook::find($bookId);
    }

    /** Show the purchase page. */
    public function purchase($bookId)
    {
        $book = $this->findBook($bookId);
        if (!$book) abort(404, 'Book not found.');

        $user = Auth::user();

        if ($user && $user->hasPurchasedBook($book->id)) {
            return redirect()->route('library.index')->with('info', 'You already own this book.');
        }

        if (!$book->isPaidItem()) {
            if (!$user) return redirect()->route('login')->with('info', 'Please login to access this free book.');
            $this->addToLibrary($user, $book);
            return redirect()->route('library.index')->with('success', 'Free book added to your library!');
        }

        $walletBalance = $user ? $user->wallet_balance : 0;

        return view('book.purchase', compact('book', 'walletBalance'));
    }

    /** Poll endpoint used by the confirming page while waiting on Pesapal. */
    public function checkPesapalStatus($paymentId)
    {
        $payment = Payment::where('user_id', Auth::id())->findOrFail($paymentId);

        // Give it one more chance to finalize right now, in case IPN/callback landed just after last check
        if ($payment->status !== 'completed' && $payment->order_tracking_id) {
            $this->finalize($payment, $payment->order_tracking_id);
            $payment->refresh();
        }

        return response()->json([
            'status' => $payment->status,
            'redirect' => $payment->status === 'completed'
                ? route('book.purchase.success', $payment->id)
                : null,
        ]);
    }

    /** Entry point from the purchase form. */
    public function processPurchase(Request $request)
    {
        $request->validate([
            'book_id' => 'required|integer',
            'payment_method' => 'required|string|in:wallet,pesapal',
        ]);

        $user = Auth::user();
        $book = $this->findBook($request->book_id);

        if (!$book) return redirect()->back()->with('error', 'Book not found.');
        if ($user->hasPurchasedBook($book->id)) {
            return redirect()->back()->with('error', 'You already own this book.');
        }
        if (!$book->isPaidItem()) {
            $this->addToLibrary($user, $book);
            return redirect()->route('library.index')->with('success', 'Free book added to your library!');
        }

        return $request->payment_method === 'wallet'
            ? $this->payWithWallet($user, $book)
            : $this->payWithPesapal($user, $book);
    }

    private function payWithWallet(User $user, $book)
    {
        if ($user->wallet_balance < $book->price) {
            return redirect()->back()->with('error', 'Insufficient wallet balance. Shortfall: TSh ' .
                number_format($book->price - $user->wallet_balance, 2));
        }

        DB::beginTransaction();
        try {
            $lockedUser = User::where('id', $user->id)->lockForUpdate()->first();
            $newBalance = $lockedUser->wallet_balance - $book->price;
            $lockedUser->update(['wallet_balance' => $newBalance]);

            $payment = Payment::create([
                'user_id' => $lockedUser->id,
                'payable_type' => get_class($book),
                'payable_id' => $book->id,
                'amount' => $book->price,
                'currency' => 'TZS',
                'status' => 'completed',
                'method' => 'wallet',
                'reference' => 'PUR_' . time() . '_' . $lockedUser->id . '_' . $book->id,
            ]);

            Transaction::create([
                'user_id' => $lockedUser->id,
                'type' => 'debit',
                'amount' => $book->price,
                'balance_after' => $newBalance,
                'description' => 'Purchase: ' . $book->title,
                'reference' => $payment->reference,
                'status' => 'completed',
                'method' => 'wallet',
                'payable_type' => get_class($book),
                'payable_id' => $book->id,
            ]);

            if ($book->author_id && $author = User::find($book->author_id)) {
                $this->commissionService->processCommission($author, $lockedUser, $book, $book->price);
            }

            $this->addToLibrary($lockedUser, $book);

            DB::commit();

            return redirect()->route('book.purchase.success', $payment->id);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Wallet purchase failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Purchase failed: ' . $e->getMessage());
        }
    }

    private function payWithPesapal(User $user, $book)
    {
        $reference = 'PUR_' . time() . '_' . $user->id . '_' . $book->id;

        $payment = Payment::create([
            'user_id' => $user->id,
            'payable_type' => get_class($book),
            'payable_id' => $book->id,
            'amount' => $book->price,
            'currency' => 'TZS',
            'status' => 'pending',
            'method' => 'pesapal',
            'reference' => $reference,
            'idempotency_key' => $reference,
        ]);

        $result = $this->pesapal->submitOrder([
            'id' => $reference,
            'currency' => 'TZS',
            'amount' => $book->price,
            'description' => 'Purchase: ' . $book->title,
            'callback_url' => route('book.purchase.pesapal.callback', ['paymentId' => $payment->id]),
            'billing_address' => $this->pesapal->buildBillingAddress($user),
        ]);

        if (!$result['success']) {
            $payment->update(['status' => 'failed', 'gateway_response' => $result]);
            return redirect()->back()->with('error', $result['message'] ?? 'Payment initiation failed.');
        }

        $payment->update(['order_tracking_id' => $result['order_tracking_id']]);

        return redirect()->away($result['redirect_url']);
    }

    /** Browser redirect back from Pesapal. */
    public function pesapalCallback(Request $request, $paymentId)
    {
        $payment = Payment::findOrFail($paymentId);
        $this->finalize($payment, $request->query('OrderTrackingId'));

        if ($payment->fresh()->status === 'completed') {
            return redirect()->route('book.purchase.success', $payment->id);
        }

        return view('book.purchase-confirming', ['payment' => $payment]);
    }

    /** Server-to-server IPN — dedicated to book purchases, isolated from other Pesapal flows. */
    public function pesapalIpn(Request $request)
    {
        $orderTrackingId = $request->query('OrderTrackingId');
        $merchantReference = $request->query('OrderMerchantReference');

        $payment = Payment::where('reference', $merchantReference)->where('method', 'pesapal')->first();

        if ($payment) {
            $this->finalize($payment, $orderTrackingId);
        }

        return response()->json([
            'orderNotificationType' => $request->query('OrderNotificationType', 'IPNCHANGE'),
            'orderTrackingId' => $orderTrackingId,
            'orderMerchantReference' => $merchantReference,
            'status' => 200,
        ]);
    }

    /** Single source of truth for completing a Pesapal purchase. Idempotent. */
    private function finalize(Payment $payment, ?string $orderTrackingId): void
    {
        if ($payment->status === 'completed' || !$orderTrackingId) return;

        $status = $this->pesapal->getOrderStatus($orderTrackingId);
        if (!$status['success'] || strtoupper($status['status']) !== 'COMPLETED') return;

        DB::transaction(function () use ($payment, $status) {
            $locked = Payment::where('id', $payment->id)->lockForUpdate()->first();

            if ($locked->status === 'completed' || $locked->webhook_processed_at !== null) return;

            $locked->update([
                'status' => 'completed',
                'gateway_response' => $status,
                'webhook_processed_at' => now(),
            ]);

            $book = $this->findBook($locked->payable_id);
            $user = User::find($locked->user_id);

            Transaction::create([
                'user_id' => $locked->user_id,
                'type' => 'debit',
                'amount' => $locked->amount,
                'balance_after' => $user->wallet_balance ?? 0,
                'description' => 'Book Purchase: ' . ($book->title ?? 'Book'),
                'reference' => $locked->reference,
                'status' => 'completed',
                'method' => $locked->method,
                'payable_type' => $book ? get_class($book) : null,
                'payable_id' => $locked->payable_id,
            ]);

            if ($book && $user) {
                $this->addToLibrary($user, $book);

                if ($book->author_id && $author = User::find($book->author_id)) {
                    $this->commissionService->processCommission($author, $user, $book, $locked->amount);
                }
            }
        });
    }

    public function purchaseSuccess($paymentId)
    {
        $payment = Payment::where('user_id', Auth::id())
            ->where('id', $paymentId)
            ->where('status', 'completed')
            ->firstOrFail();

        $book = $this->findBook($payment->payable_id);

        return view('book.purchase-success', compact('payment', 'book'));
    }

    public function purchaseHistory()
    {
        $purchases = Payment::where('user_id', Auth::id())
            ->where('status', 'completed')
            ->whereIn('payable_type', [Book::class, BookshopBook::class])
            ->latest()
            ->paginate(10);

        return view('book.purchase-history', compact('purchases'));
    }

    public function downloadBook($bookId)
    {
        $user = Auth::user();
        $book = $this->findBook($bookId);
        if (!$book) abort(404, 'Book not found.');

        $isFree = !$book->isPaidItem();
        if (!$isFree && !$user->hasPurchasedBook($book->id)) {
            return redirect()->back()->with('error', 'You need to purchase this book to download it.');
        }

        if (!$book->file_path) {
            return redirect()->back()->with('error', 'No file available for download.');
        }

        $filePath = storage_path('app/public/' . $book->file_path);
        if (!file_exists($filePath)) {
            Log::error('Book file not found', ['book_id' => $book->id, 'file_path' => $book->file_path, 'user_id' => $user->id]);
            return redirect()->back()->with('error', 'The file for this book is currently unavailable. Please contact support.');
        }

        if (isset($book->downloads)) $book->increment('downloads');

        return response()->download($filePath);
    }

    private function addToLibrary($user, $book): void
    {
        $user->books()->syncWithoutDetaching([
            $book->id => ['purchased_at' => now(), 'status' => 'want_to_read']
        ]);
    }
}