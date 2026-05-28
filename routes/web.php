<?php

use App\Http\Controllers\FrontController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LibraryController;
use App\Http\Controllers\CommunityController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AIController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\ConverterController;
use App\Http\Controllers\MarketplaceController;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\BookController as AdminBookController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\MarketplaceController as AdminMarketplaceController;
use App\Http\Controllers\Admin\PaymentController as AdminPaymentController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\BookmarkController;
use App\Http\Controllers\RatingReviewController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\ReferralController;
use App\Http\Controllers\Auth\SocialiteController;


// ==========================================
// PUBLIC ROUTES (No login required)
// ==========================================
Route::get('/', [FrontController::class, 'welcome'])->name('welcome');
Route::get('/home', [FrontController::class, 'home'])->name('home');

// Application Routes (for users)
Route::middleware(['auth'])->group(function () {
    Route::get('/apply/{type}', [App\Http\Controllers\ApplicationController::class, 'create'])->name('applications.create');
    Route::post('/applications', [App\Http\Controllers\ApplicationController::class, 'store'])->name('applications.store');
});

// Admin Application Management
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/applications', [App\Http\Controllers\ApplicationController::class, 'index'])->name('applications.index');
    Route::get('/applications/{application}', [App\Http\Controllers\ApplicationController::class, 'show'])->name('applications.show');
    Route::post('/applications/{application}/approve', [App\Http\Controllers\ApplicationController::class, 'approve'])->name('applications.approve');
    Route::post('/applications/{application}/reject', [App\Http\Controllers\ApplicationController::class, 'reject'])->name('applications.reject');
    Route::get('/applications/{application}/download/{document}', [App\Http\Controllers\ApplicationController::class, 'download'])->name('applications.download');
});

// Institution Dashboard (for institution admins)
Route::get('/institution/dashboard', [App\Http\Controllers\Institution\DashboardController::class, 'index'])
    ->middleware(['auth', 'institution'])
    ->name('institution.dashboard');

// Institution Routes (protected by institution middleware)
Route::middleware(['auth', 'institution'])->prefix('institution')->name('institution.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Institution\DashboardController::class, 'index'])->name('dashboard');
    Route::resource('members', App\Http\Controllers\Institution\MemberController::class);
});

// ==========================================
// SOCIAL LOGIN ROUTES (MUST BE OUTSIDE AUTH MIDDLEWARE)
// ==========================================
Route::prefix('auth')->name('auth.')->group(function () {
    Route::get('/google', [SocialiteController::class, 'redirectToGoogle'])->name('google');
    Route::get('/google/callback', [SocialiteController::class, 'handleGoogleCallback'])->name('google.callback');
    Route::get('/github', [SocialiteController::class, 'redirectToGitHub'])->name('github');
    Route::get('/github/callback', [SocialiteController::class, 'handleGitHubCallback'])->name('github.callback');
});

// ==========================================
// REFERRAL PROCESS (Public - no auth needed to process referral link)
// ==========================================
Route::get('/refer/{code}', [ReferralController::class, 'processReferral'])->name('referral.process');

// ==========================================
// AUTHENTICATED ROUTES (Login required)
// ==========================================
Route::middleware(['auth'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
  Route::get('/leaderboard', [App\Http\Controllers\LeaderboardController::class, 'index'])->name('leaderboard.index');
  Route::get('/leaderboard/data', [App\Http\Controllers\LeaderboardController::class, 'index'])->name('leaderboard.data');


    Route::get('/wallet/balance', [App\Http\Controllers\WalletController::class, 'getBalance'])->name('wallet.balance');
    // ==========================================
    // LIBRARY SYSTEM
    // ==========================================
    Route::prefix('library')->name('library.')->group(function () {
        Route::get('/', [LibraryController::class, 'index'])->name('index');
        Route::get('/my-library', [LibraryController::class, 'myLibrary'])->name('my-library');
        Route::get('/{book}', [LibraryController::class, 'show'])->name('show');
        Route::get('/{book}/read', [LibraryController::class, 'read'])->name('read');
        Route::get('/{book}/download', [LibraryController::class, 'download'])->name('download');
        Route::post('/{book}/progress', [LibraryController::class, 'updateProgress'])->name('progress');
        Route::post('/{book}/add-to-library', [LibraryController::class, 'addToLibrary'])->name('add-to-library');
    });
        // ==========================================
    // BOOK PURCHASE ROUTES (Add this new section)
    // ==========================================
    Route::prefix('books')->name('books.')->group(function () {
        Route::get('/{book}/purchase-info', [App\Http\Controllers\BookPurchaseController::class, 'purchaseInfo'])->name('purchase-info');
        Route::post('/{book}/purchase-wallet', [App\Http\Controllers\BookPurchaseController::class, 'purchaseWithWallet'])->name('purchase-wallet');
        Route::get('/{book}/check-purchase', [App\Http\Controllers\BookPurchaseController::class, 'checkPurchase'])->name('check-purchase');
    });

        // ==========================================
    // MULTI-PAYMENT ROUTES
    // ==========================================
    Route::prefix('payment')->name('payment.')->group(function () {
        Route::get('/methods', [App\Http\Controllers\MultiPaymentController::class, 'showMethods'])->name('methods');
        Route::post('/initiate', [App\Http\Controllers\MultiPaymentController::class, 'initiatePayment'])->name('initiate');
        Route::post('/save-details', [App\Http\Controllers\MultiPaymentController::class, 'savePaymentDetails'])->name('save-details');
       Route::get('/status/{paymentId}', [App\Http\Controllers\MultiPaymentController::class, 'checkStatus'])->name('status');
    });

    // Referral Routes (Authenticated)
    Route::middleware(['auth'])->prefix('referrals')->name('referrals.')->group(function () {
        Route::get('/', [ReferralController::class, 'index'])->name('index');
        Route::post('/{id}/complete', [ReferralController::class, 'markComplete'])->name('complete');
    });

    // Search Routes
    Route::prefix('search')->name('search.')->group(function () {
        Route::get('/', [SearchController::class, 'index'])->name('index');
        Route::get('/live', [SearchController::class, 'live'])->name('live');
        Route::get('/filter', [SearchController::class, 'filter'])->name('filter');
    });

    // Notification Routes
 Route::middleware(['auth'])->prefix('notifications')->name('notifications.')->group(function () {
    Route::get('/', [NotificationController::class, 'index'])->name('index');
    
    // ✅ Specific named routes FIRST
    Route::get('/unread-count', [NotificationController::class, 'getUnreadCount'])->name('unread-count');
    Route::get('/latest', [NotificationController::class, 'getLatest'])->name('latest');
    Route::post('/mark-all-read', [NotificationController::class, 'markAllRead'])->name('mark-all-read');
    
    // ⬇️ Dynamic /{id} routes LAST
    Route::post('/{id}/read', [NotificationController::class, 'markAsRead'])->name('mark-read');
    Route::delete('/{id}', [NotificationController::class, 'destroy'])->name('destroy');
 });

    // Bookmark Routes
    Route::middleware(['auth'])->group(function () {
        Route::post('/bookmark/toggle', [BookmarkController::class, 'toggle'])->name('bookmark.toggle');
        Route::get('/bookmarks', [BookmarkController::class, 'index'])->name('bookmarks.index');
        Route::delete('/bookmarks/{id}', [BookmarkController::class, 'destroy'])->name('bookmark.destroy');
    });

    // Rating & Review Routes
    Route::middleware(['auth'])->group(function () {
        Route::post('/books/{book}/rate', [RatingReviewController::class, 'rate'])->name('books.rate');
        Route::post('/books/{book}/review', [RatingReviewController::class, 'review'])->name('books.review');
        Route::post('/reviews/{review}/helpful', [RatingReviewController::class, 'helpful'])->name('reviews.helpful');
        Route::delete('/books/{book}/rating', [RatingReviewController::class, 'deleteRating'])->name('books.rating.delete');
        Route::delete('/books/{book}/review', [RatingReviewController::class, 'deleteReview'])->name('books.review.delete');
    });

    // Quiz Routes
    Route::middleware(['auth'])->group(function () {
        Route::get('/quizzes', [QuizController::class, 'index'])->name('quizzes.index');
        Route::get('/quizzes/{id}', [QuizController::class, 'show'])->name('quizzes.show');
        Route::post('/quizzes/{id}/submit', [QuizController::class, 'submit'])->name('quizzes.submit');
        Route::get('/quizzes/results/{attemptId}', [QuizController::class, 'results'])->name('quizzes.results');
        Route::get('/quizzes/history', [QuizController::class, 'history'])->name('quizzes.history');
    });

  // Wallet Routes
 Route::middleware(['auth'])->prefix('wallet')->name('wallet.')->group(function () {
    Route::get('/', [App\Http\Controllers\WalletController::class, 'index'])->name('index');
    Route::post('/withdraw', [App\Http\Controllers\WalletController::class, 'withdraw'])->name('withdraw');
    Route::post('/topup', [App\Http\Controllers\WalletController::class, 'topUp'])->name('topup');
    Route::get('/balance', [App\Http\Controllers\WalletController::class, 'getBalance'])->name('balance');
 });
    // Profile Routes
    Route::middleware(['auth'])->prefix('profile')->name('profile.')->group(function () {
        Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
        Route::put('/update', [ProfileController::class, 'update'])->name('update');
        Route::post('/avatar', [ProfileController::class, 'updateAvatar'])->name('avatar');
        Route::post('/cover', [ProfileController::class, 'updateCover'])->name('cover');
        Route::delete('/avatar', [ProfileController::class, 'deleteAvatar'])->name('avatar.delete');
        Route::delete('/cover', [ProfileController::class, 'deleteCover'])->name('cover.delete');
    });

    // AI Routes
    Route::middleware(['auth'])->group(function () {
        Route::get('/ai/chat', [AIController::class, 'index'])->name('ai.chat');
        Route::get('/ai/chat/{chat_session}', [AIController::class, 'index'])->name('ai.chat.session');
        Route::post('/ai/send', [AIController::class, 'sendMessage'])->name('ai.send');
        Route::post('/ai/new-session', [AIController::class, 'newSession'])->name('ai.new');
        Route::delete('/ai/session/{id}', [AIController::class, 'deleteSession'])->name('ai.delete');
        Route::get('/ai/session/{id}', [AIController::class, 'getSession'])->name('ai.get');
    });

    // Certificate Routes
    Route::prefix('certificates')->name('certificates.')->group(function () {
        Route::get('/', [CertificateController::class, 'index'])->name('index');
        Route::get('/generate/{book}', [CertificateController::class, 'generate'])->name('generate');
        Route::get('/show/{certificate}', [CertificateController::class, 'show'])->name('show');
        Route::get('/download/{certificate}', [CertificateController::class, 'download'])->name('download');
    });

    // Community System
    Route::prefix('community')->name('community.')->group(function () {
        Route::get('/', [CommunityController::class, 'index'])->name('index');
        Route::get('/my-groups', [CommunityController::class, 'myGroups'])->name('my-groups');
        Route::get('/create', [CommunityController::class, 'create'])->name('create');
        Route::post('/store', [CommunityController::class, 'store'])->name('store');
        Route::get('/{group}', [CommunityController::class, 'show'])->name('show');
        Route::post('/{group}/join', [CommunityController::class, 'join'])->name('join');
        Route::delete('/{group}/leave', [CommunityController::class, 'leave'])->name('leave');
        Route::post('/{group}/message', [CommunityController::class, 'sendMessage'])->name('send-message');
        Route::delete('/{group}/message/{message}', [CommunityController::class, 'deleteMessage'])->name('delete-message');
        Route::get('/{group}/messages/{lastId?}', [CommunityController::class, 'getMessages'])->name('messages');
    });

    // Converter Routes
    Route::prefix('converter')->name('converter.')->group(function () {
        Route::get('/', [ConverterController::class, 'index'])->name('index');
        Route::post('/pdf-to-word', [ConverterController::class, 'pdfToWord'])->name('pdf-to-word');
        Route::post('/word-to-pdf', [ConverterController::class, 'wordToPdf'])->name('word-to-pdf');
        Route::post('/book-to-audio', [ConverterController::class, 'bookToAudio'])->name('book-to-audio');
    });

    // Marketplace Routes
    Route::prefix('marketplace')->name('marketplace.')->group(function () {
        Route::get('/', [MarketplaceController::class, 'index'])->name('index');
        Route::get('/my-listings', [MarketplaceController::class, 'myListings'])->name('my-listings');
        Route::get('/create', [MarketplaceController::class, 'create'])->name('create');
        Route::post('/store', [MarketplaceController::class, 'store'])->name('store');
        Route::get('/{listing}', [MarketplaceController::class, 'show'])->name('show');
        Route::get('/{listing}/download', [MarketplaceController::class, 'download'])->name('download');
        Route::delete('/{listing}', [MarketplaceController::class, 'destroy'])->name('destroy');
    });

    // Document Routes
    Route::prefix('documents')->name('documents.')->group(function () {
        Route::get('/', [DocumentController::class, 'index'])->name('index');
        Route::get('/create', [DocumentController::class, 'create'])->name('create');
        Route::post('/upload', [DocumentController::class, 'upload'])->name('upload');
        Route::get('/{document}', [DocumentController::class, 'show'])->name('show');
        Route::post('/{document}/ask', [DocumentController::class, 'ask'])->name('ask');
        Route::delete('/{document}', [DocumentController::class, 'destroy'])->name('destroy');
    });
});

// Payment Webhooks (Real callbacks from payment providers)
Route::post('/webhooks/mpesa', [App\Http\Controllers\PaymentWebhookController::class, 'handleMpesaCallback']);
Route::post('/webhooks/stripe', [App\Http\Controllers\PaymentWebhookController::class, 'handleStripeWebhook']);

// Admin: Approve bank transfers
Route::middleware(['auth', 'admin'])->post('/admin/payments/{paymentId}/approve', [App\Http\Controllers\PaymentWebhookController::class, 'approveBankTransfer'])->name('admin.payments.approve');

// ==========================================
// PAYMENT CALLBACKS (Public)
// ==========================================
Route::post('/api/payments/mpesa/callback', [App\Http\Controllers\MultiPaymentController::class, 'mpesaCallback'])->name('payment.mpesa.callback');
Route::post('/api/payments/tigopesa/callback', [App\Http\Controllers\MultiPaymentController::class, 'tigopesaCallback'])->name('payment.tigopesa.callback');
Route::post('/api/payments/halopesa/callback', [App\Http\Controllers\MultiPaymentController::class, 'halopesaCallback'])->name('payment.halopesa.callback');
Route::post('/stripe/webhook', [App\Http\Controllers\MultiPaymentController::class, 'stripeWebhook'])->name('stripe.webhook');

// ADMIN ROUTES 
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    
 // Payments Management
    Route::prefix('payments')->name('payments.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\PaymentController::class, 'index'])->name('index');
        Route::get('/{id}', [App\Http\Controllers\Admin\PaymentController::class, 'show'])->name('show');
        Route::post('/approve-withdrawal/{id}', [App\Http\Controllers\Admin\PaymentController::class, 'approveWithdrawal'])->name('approve-withdrawal');
        Route::post('/reject-withdrawal/{id}', [App\Http\Controllers\Admin\PaymentController::class, 'rejectWithdrawal'])->name('reject-withdrawal');
        Route::post('/approve-deposit/{id}', [App\Http\Controllers\Admin\PaymentController::class, 'approveDeposit'])->name('approve-deposit');
    });
    
// Admin Dashboard
    Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/analytics', [App\Http\Controllers\Admin\AnalyticsController::class, 'index'])->name('analytics');
    Route::get('/analytics/data', [App\Http\Controllers\Admin\AnalyticsController::class, 'getData'])->name('analytics.data');
    
    // ==========================================
    // BOOKS MANAGEMENT
    // ==========================================
    Route::resource('books', App\Http\Controllers\Admin\BookController::class);
    Route::post('/books/{book}/toggle-status', [App\Http\Controllers\Admin\BookController::class, 'toggleStatus'])->name('books.toggle-status');
    Route::post('/books/bulk-action', [App\Http\Controllers\Admin\BookController::class, 'bulkAction'])->name('books.bulk-action');
    
    // ==========================================
    // USERS MANAGEMENT
    // ==========================================
    Route::resource('users', App\Http\Controllers\Admin\UserController::class);
    Route::post('/users/{user}/toggle-role', [App\Http\Controllers\Admin\UserController::class, 'toggleRole'])->name('users.toggle-role');
    
    // ==========================================
    // INSTITUTIONS MANAGEMENT
    // ==========================================
    Route::resource('institutions', App\Http\Controllers\Admin\InstitutionController::class);
    Route::post('/institutions/{institution}/approve', [App\Http\Controllers\Admin\InstitutionController::class, 'approve'])->name('institutions.approve');
    Route::post('/institutions/{institution}/reject', [App\Http\Controllers\Admin\InstitutionController::class, 'reject'])->name('institutions.reject');
    Route::post('/institutions/{institution}/suspend', [App\Http\Controllers\Admin\InstitutionController::class, 'suspend'])->name('institutions.suspend');
    
    // Institution Members Management (Note: use 'institutions.members' not 'admin.institutions.members')
    Route::get('/institutions/{institution}/members', [App\Http\Controllers\Admin\InstitutionMemberController::class, 'index'])->name('institutions.members');
    Route::get('/institutions/{institution}/members/create', [App\Http\Controllers\Admin\InstitutionMemberController::class, 'create'])->name('institutions.members.create');
    Route::post('/institutions/{institution}/members', [App\Http\Controllers\Admin\InstitutionMemberController::class, 'store'])->name('institutions.members.store');
    Route::delete('/institutions/{institution}/members/{member}', [App\Http\Controllers\Admin\InstitutionMemberController::class, 'destroy'])->name('institutions.members.destroy');
    
    // ==========================================
    // MARKETPLACE MANAGEMENT
    // ==========================================
    Route::get('/marketplace/pending', [App\Http\Controllers\Admin\MarketplaceController::class, 'pending'])->name('marketplace.pending');
    Route::get('/marketplace/all', [App\Http\Controllers\Admin\MarketplaceController::class, 'all'])->name('marketplace.all');
    Route::post('/marketplace/{listing}/approve', [App\Http\Controllers\Admin\MarketplaceController::class, 'approve'])->name('marketplace.approve');
    Route::post('/marketplace/{listing}/reject', [App\Http\Controllers\Admin\MarketplaceController::class, 'reject'])->name('marketplace.reject');
    
    // ==========================================
    // PAYMENTS MANAGEMENT
    // ==========================================
    Route::get('/payments', [App\Http\Controllers\Admin\PaymentController::class, 'index'])->name('payments.index');
});
// ==========================================
// PUBLIC PROFILE ROUTE
// ==========================================
Route::get('/@{username}', [ProfileController::class, 'show'])->name('profile.show');

// ==========================================
// AUTHENTICATION ROUTES (Laravel Breeze/Jetstream)
// ==========================================
require __DIR__.'/auth.php';