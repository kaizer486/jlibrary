<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Transaction;
use App\Models\SubscriptionPayment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    /**
     * Generate payment invoice
     */
    public function paymentInvoice($paymentId)
    {
        $payment = Payment::with('user')->findOrFail($paymentId);
        
        // Check authorization
        if ($payment->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }
        
        $data = [
            'invoice_number' => $payment->reference ?? 'INV-' . $payment->id,
            'date' => $payment->created_at,
            'user' => $payment->user,
            'amount' => $payment->amount,
            'method' => $payment->method ?? 'N/A',
            'status' => $payment->status,
            'type' => $payment->payable_type === 'App\\Models\\Book' ? 'Book Purchase' : 'Wallet Deposit',
            'item_name' => $payment->payable_type === 'App\\Models\\Book' 
                ? optional($payment->payable)->title 
                : 'Wallet Deposit',
        ];
        
        $pdf = Pdf::loadView('invoices.payment', $data);
        
        return $pdf->download("invoice_{$data['invoice_number']}.pdf");
    }
    
    /**
     * Generate transaction invoice
     */
    public function transactionInvoice($transactionId)
    {
        $transaction = Transaction::with('user')->findOrFail($transactionId);
        
        if ($transaction->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }
        
        $data = [
            'invoice_number' => $transaction->reference ?? 'TXN-' . $transaction->id,
            'date' => $transaction->created_at,
            'user' => $transaction->user,
            'amount' => $transaction->amount,
            'type' => $transaction->type,
            'description' => $transaction->description,
            'balance_after' => $transaction->balance_after,
            'status' => $transaction->status,
        ];
        
        $pdf = Pdf::loadView('invoices.transaction', $data);
        
        return $pdf->download("invoice_{$data['invoice_number']}.pdf");
    }
    
    /**
     * Generate subscription invoice
     */
    public function subscriptionInvoice($subscriptionPaymentId)
    {
        $payment = SubscriptionPayment::with('subscription')->findOrFail($subscriptionPaymentId);
        $subscription = $payment->subscription;
        
        $data = [
            'invoice_number' => $payment->invoice_number,
            'date' => $payment->created_at,
            'paid_at' => $payment->paid_at,
            'amount' => $payment->amount,
            'currency' => $payment->currency,
            'plan_name' => $subscription->plan ?? 'Subscription',
            'billing_period_start' => $payment->billing_period_start,
            'billing_period_end' => $payment->billing_period_end,
            'status' => $payment->status,
        ];
        
        $pdf = Pdf::loadView('invoices.subscription', $data);
        
        return $pdf->download("invoice_{$payment->invoice_number}.pdf");
    }
}