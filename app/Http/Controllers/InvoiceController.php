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
        if ($payment->user_id !== Auth::id() && !Auth::user()->isSuperAdmin()) {
            abort(403, 'Unauthorized access to this invoice.');
        }
        
        $data = [
            'invoice_number' => $payment->reference ?? 'INV-' . str_pad($payment->id, 6, '0', STR_PAD_LEFT),
            'date' => $payment->created_at,
            'user' => $payment->user,
            'amount' => $payment->amount,
            'method' => $payment->method ?? 'N/A',
            'status' => $payment->status,
            'type' => $payment->payable_type === 'App\\Models\\Book' ? 'Book Purchase' : 'Wallet Deposit',
            'item_name' => $payment->payable_type === 'App\\Models\\Book' 
                ? optional($payment->payable)->title ?? 'Book Purchase'
                : 'Wallet Deposit',
            'reference' => $payment->reference,
        ];
        
        // Check if super admin view exists, otherwise use regular
        $view = 'invoices.payment';
        if (Auth::user()->isSuperAdmin() && view()->exists('super-admin.invoices.payment-invoice')) {
            $view = 'super-admin.invoices.payment-invoice';
        }
        
        $pdf = Pdf::loadView($view, $data);
        return $pdf->download("invoice_{$data['invoice_number']}.pdf");
    }
    
    /**
     * Generate transaction invoice
     */
    public function transactionInvoice($transactionId)
    {
        $transaction = Transaction::with('user')->findOrFail($transactionId);
        
        if ($transaction->user_id !== Auth::id() && !Auth::user()->isSuperAdmin()) {
            abort(403, 'Unauthorized access to this invoice.');
        }
        
        $data = [
            'invoice_number' => $transaction->reference ?? 'TXN-' . str_pad($transaction->id, 6, '0', STR_PAD_LEFT),
            'date' => $transaction->created_at,
            'user' => $transaction->user,
            'amount' => $transaction->amount,
            'type' => ucfirst($transaction->type),
            'method' => $transaction->method ?? 'N/A',
            'description' => $transaction->description ?? ucfirst($transaction->type) . ' Transaction',
            'balance_after' => $transaction->balance_after,
            'status' => $transaction->status,
            'reference' => $transaction->reference,
        ];
        
        $view = 'invoices.transaction';
        if (Auth::user()->isSuperAdmin() && view()->exists('super-admin.invoices.transaction-invoice')) {
            $view = 'super-admin.invoices.transaction-invoice';
        }
        
        $pdf = Pdf::loadView($view, $data);
        return $pdf->download("invoice_{$data['invoice_number']}.pdf");
    }
    
    /**
     * Generate subscription invoice
     */
    public function subscriptionInvoice($subscriptionPaymentId)
    {
        $payment = SubscriptionPayment::with('subscription')->findOrFail($subscriptionPaymentId);
        $subscription = $payment->subscription;
        
        if ($subscription->user_id !== Auth::id() && !Auth::user()->isSuperAdmin()) {
            abort(403, 'Unauthorized access to this invoice.');
        }
        
        $data = [
            'invoice_number' => $payment->invoice_number ?? 'SUB-' . str_pad($payment->id, 6, '0', STR_PAD_LEFT),
            'date' => $payment->created_at,
            'paid_at' => $payment->paid_at ?? $payment->created_at,
            'user' => $subscription->user,
            'amount' => $payment->amount,
            'currency' => $payment->currency ?? 'TSh',
            'plan_name' => $subscription->plan ?? 'Subscription',
            'billing_period_start' => $payment->billing_period_start ?? $payment->created_at,
            'billing_period_end' => $payment->billing_period_end ?? $payment->created_at->addMonth(),
            'status' => $payment->status,
            'method' => $payment->method ?? 'N/A',
            'reference' => $payment->reference,
        ];
        
        $view = 'invoices.subscription';
        if (Auth::user()->isSuperAdmin() && view()->exists('super-admin.invoices.subscription-invoice')) {
            $view = 'super-admin.invoices.subscription-invoice';
        }
        
        $pdf = Pdf::loadView($view, $data);
        return $pdf->download("invoice_{$data['invoice_number']}.pdf");
    }
}