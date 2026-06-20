<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\WalletController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\TransactionController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// ==========================================
// PUBLIC ROUTES (No authentication)
// ==========================================
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Webhook routes (no auth)
Route::post('/webhooks/mpesa', [App\Http\Controllers\PaymentWebhookController::class, 'handleMpesaCallback']);
Route::post('/webhooks/tigopesa', [App\Http\Controllers\PaymentWebhookController::class, 'handleTigopesaCallback']);
Route::post('/webhooks/halopesa', [App\Http\Controllers\PaymentWebhookController::class, 'handleHalopesaCallback']);
Route::post('/webhooks/stripe', [App\Http\Controllers\PaymentWebhookController::class, 'handleStripeWebhook']);
Route::post('/webhooks/pesapal', [App\Http\Controllers\PaymentWebhookController::class, 'handlePesapalCallback']);

// ==========================================
// AUTHENTICATED ROUTES (require token)
// ==========================================
Route::middleware('auth:sanctum')->group(function () {
    
    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/logout-all', [AuthController::class, 'logoutAllDevices']);
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/refresh-token', [AuthController::class, 'refreshToken']);
    
    // Update payment details
Route::post('/payment-details/update', [AuthController::class, 'updatePaymentDetails']);

// Get available payment methods
Route::get('/payment-methods', [AuthController::class, 'getPaymentMethods']);
    // ==========================================
    // WALLET API
    // ==========================================
    Route::prefix('wallet')->name('api.wallet.')->group(function () {
        Route::get('/balance', [WalletController::class, 'balance'])->name('balance');
        Route::get('/summary', [WalletController::class, 'summary'])->name('summary');
        Route::post('/withdraw', [WalletController::class, 'withdraw'])->name('withdraw');
    });
    
    // ==========================================
    // TRANSACTION API
    // ==========================================
    Route::prefix('transactions')->name('api.transactions.')->group(function () {
        Route::get('/', [TransactionController::class, 'index'])->name('index');
        Route::get('/stats', [TransactionController::class, 'stats'])->name('stats');
        Route::get('/export', [TransactionController::class, 'export'])->name('export');
        Route::get('/{id}', [TransactionController::class, 'show'])->name('show');
    });
    
    // ==========================================
    // PAYMENT API
    // ==========================================
    Route::prefix('payment')->name('api.payment.')->group(function () {
        Route::get('/methods', [PaymentController::class, 'getMethods'])->name('methods');
        Route::post('/initiate', [PaymentController::class, 'initiatePayment'])->name('initiate');
        Route::get('/history', [PaymentController::class, 'history'])->name('history');
        Route::get('/status/{paymentId}', [PaymentController::class, 'checkStatus'])->name('status');
        Route::get('/{paymentId}', [PaymentController::class, 'show'])->name('show');
        Route::post('/{paymentId}/cancel', [PaymentController::class, 'cancel'])->name('cancel');
    });
    
    // ==========================================
    // INVOICE API
    // ==========================================
    Route::prefix('invoices')->name('api.invoices.')->group(function () {
        Route::get('/payment/{paymentId}', [App\Http\Controllers\InvoiceController::class, 'paymentInvoice'])->name('payment');
        Route::get('/transaction/{transactionId}', [App\Http\Controllers\InvoiceController::class, 'transactionInvoice'])->name('transaction');
    });
});