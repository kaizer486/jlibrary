<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;

// ==========================================
// CONTROLLER IMPORTS
// ==========================================

// Front & General
use App\Http\Controllers\FrontController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GlobalSearchController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReferralController;
use App\Http\Controllers\BookmarkController;
use App\Http\Controllers\RatingReviewController;
use App\Http\Controllers\ConverterController;

// Library & Books
use App\Http\Controllers\LibraryController;
use App\Http\Controllers\UserLibraryController;
use App\Http\Controllers\BookPurchaseController;

// AI & Documents
use App\Http\Controllers\AIController;
use App\Http\Controllers\DocumentController;

// Community
use App\Http\Controllers\CommunityController;

// Certificates
use App\Http\Controllers\CertificateController;

// Profile
use App\Http\Controllers\ProfileController;

// Wallet & Payments
use App\Http\Controllers\WalletController;
use App\Http\Controllers\AuthorWalletController;
use App\Http\Controllers\MultiPaymentController;
use App\Http\Controllers\PaymentWebhookController;

// Quizzes
use App\Http\Controllers\QuizController;

// Institutions
use App\Http\Controllers\InstitutionController;
use App\Http\Controllers\InstitutionCreationController;
use App\Http\Controllers\JoinRequestController;
use App\Http\Controllers\PublicController;

// Marketplace & Seller
use App\Http\Controllers\MarketplaceController;
use App\Http\Controllers\SellerController;

// Author
use App\Http\Controllers\Author\DashboardController as AuthorDashboardController;
use App\Http\Controllers\Author\BookController as AuthorBookController;
use App\Http\Controllers\Author\EarningController;
use App\Http\Controllers\Author\WithdrawalController;
use App\Http\Controllers\Author\RoyaltyController;
use App\Http\Controllers\Author\MarketplaceController as AuthorMarketplaceController;

// Seller Controllers
use App\Http\Controllers\Seller\ListingController as SellerListingController;
use App\Http\Controllers\Seller\OrderController as SellerOrderController;

// Admin
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\BookController as AdminBookController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\MarketplaceController as AdminMarketplaceController;
use App\Http\Controllers\Admin\PaymentController as AdminPaymentController;
use App\Http\Controllers\Admin\ApplicationController as AdminApplicationController;
use App\Http\Controllers\Admin\AnalyticsController;

// Super Admin
use App\Http\Controllers\SuperAdmin\DashboardController as SuperAdminDashboardController;
use App\Http\Controllers\SuperAdmin\AnalyticsController as SuperAdminAnalyticsController;
use App\Http\Controllers\SuperAdmin\PaymentController as SuperAdminPaymentController;
use App\Http\Controllers\SuperAdmin\ApplicationController as SuperAdminApplicationController;
use App\Http\Controllers\SuperAdmin\SubscriptionController as SuperAdminSubscriptionController;
use App\Http\Controllers\SuperAdmin\InstitutionSubscriptionController as SuperAdminInstitutionSubscriptionController;
use App\Http\Controllers\SuperAdmin\InstitutionRequestController;

// Institution Admin
use App\Http\Controllers\Institution\DashboardController as InstitutionDashboardController;
use App\Http\Controllers\Institution\LibraryController as InstitutionLibraryController;
use App\Http\Controllers\Institution\BookController as InstitutionBookController;
use App\Http\Controllers\Institution\ShelfController as InstitutionShelfController;
use App\Http\Controllers\Institution\MemberController as InstitutionMemberController;
use App\Http\Controllers\Institution\JoinRequestController as InstitutionJoinRequestController;
use App\Http\Controllers\Institution\BorrowingController as InstitutionBorrowingController;
use App\Http\Controllers\Institution\WithdrawalController as InstitutionWithdrawalController;
use App\Http\Controllers\Institution\ReportController as InstitutionReportController;
use App\Http\Controllers\Institution\SettingController as InstitutionSettingController;
use App\Http\Controllers\Institution\OrderController as InstitutionOrderController;
use App\Http\Controllers\Institution\SubscriptionController as InstitutionSubscriptionController;

// Librarian
use App\Http\Controllers\Librarian\DashboardController as LibrarianDashboardController;
use App\Http\Controllers\Librarian\BookController as LibrarianBookController;
use App\Http\Controllers\Librarian\ShelfController as LibrarianShelfController;
use App\Http\Controllers\Librarian\MemberController as LibrarianMemberController;
use App\Http\Controllers\Librarian\ReportController as LibrarianReportController;
use App\Http\Controllers\Librarian\JoinRequestController as LibrarianJoinRequestController;
use App\Http\Controllers\Librarian\SettingController as LibrarianSettingController;
use App\Http\Controllers\Librarian\BorrowingController as LibrarianBorrowingController;

// Instructor
use App\Http\Controllers\Instructor\DashboardController as InstructorDashboardController;
use App\Http\Controllers\Instructor\CourseController as InstructorCourseController;

// Borrow Requests
use App\Http\Controllers\BorrowRequestController;

// Quotes
use App\Http\Controllers\QuoteController;

// Invoices
use App\Http\Controllers\InvoiceController;

// Leaderboard
use App\Http\Controllers\LeaderboardController;

// User Subscription
use App\Http\Controllers\User\SubscriptionController as UserSubscriptionController;

// Middleware
use App\Http\Middleware\MediaTeamMiddleware;
use App\Http\Middleware\LibrarianMiddleware;
use Laravel\Socialite\Facades\Socialite;

// ==========================================
// SECTION 1: FILE SERVING & STORAGE ROUTES
// ==========================================

Route::get('/media/{path}', function ($path) {
    $fullPath = storage_path('app/public/' . $path);
    if (!file_exists($fullPath)) abort(404);
    return response()->file($fullPath, [
        'Content-Type' => File::mimeType($fullPath),
        'Cache-Control' => 'public, max-age=31536000',
    ]);
})->where('path', '.*');

Route::get('/storage/{path}', function ($path) {
    $fullPath = storage_path('app/public/' . $path);
    if (!file_exists($fullPath)) abort(404);
    return response()->file($fullPath, [
        'Content-Type' => mime_content_type($fullPath),
    ]);
})->where('path', '.*')->name('storage.local');


Route::get('/auth/google', function () {
    return Socialite::driver('google')->redirect();
})->name('auth.google');

Route::get('/auth/google/callback', function () {
    $googleUser = Socialite::driver('google')->user();
    
    // Find or create user
    $user = \App\Models\User::updateOrCreate([
        'email' => $googleUser->email,
    ], [
        'name' => $googleUser->name,
        'google_id' => $googleUser->id,
        'avatar' => $googleUser->avatar,
        'email_verified_at' => now(),
    ]);

    auth()->login($user);

    return redirect('/dashboard');
})->name('auth.google.callback');

// API Endpoint for Download Status
Route::get('/api/download-status', function() {
    if (!auth()->check()) {
        return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
    }
    
    $user = auth()->user();
    $used = DB::table('download_logs')
        ->where('user_id', $user->id)
        ->whereDate('downloaded_at', today())
        ->count();
    $limit = 5;
    $remaining = max(0, $limit - $used);
    
    return response()->json([
        'success' => true,
        'used' => $used,
        'remaining' => $remaining,
        'limit' => $limit,
        'progress' => min(100, round(($used / $limit) * 100))
    ]);
})->name('api.download.status');

// ==========================================
// SECTION 2: PUBLIC ROUTES (No login required)
// ==========================================

Route::get('/', [FrontController::class, 'welcome'])->name('welcome');
Route::get('/home', [FrontController::class, 'home'])->name('home');

Route::get('/health', function () {
    return response()->json([
        'status' => 'healthy',
        'timestamp' => now()->toISOString(),
        'environment' => app()->environment(),
        'version' => '1.0.0',
    ]);
});

// Local test webhook
if (app()->environment('local')) {
    Route::post('/test-webhook/mpesa', function () {
        return response()->json(['message' => 'Test webhook received']);
    });
}

// ==========================================
// SECTION 2.1: PUBLIC INSTITUTION LIBRARY ROUTES
// ==========================================

Route::prefix('institution')->name('institution.public.')->group(function () {
    // Main library view
    Route::get('/{institutionId}/library', [PublicController::class, 'index'])->name('index');
    
    // Book view - uses ID (numeric)
    Route::get('/{institutionId}/library/{book:id}', [PublicController::class, 'show'])->name('show');
    
    // Book view by slug (SEO friendly URLs)
    Route::get('/{institutionId}/library/slug/{slug}', [PublicController::class, 'showBySlug'])->name('show.slug');
    
    // Book view by ISBN
    Route::get('/{institutionId}/library/isbn/{isbn}', [PublicController::class, 'showByIsbn'])->name('show.isbn');
    
    // Read book (NEW)
    Route::get('/{institutionId}/library/{book}/read', [PublicController::class, 'read'])->name('read');
    
    // Download book (NEW - with explicit ID binding)
    Route::get('/{institutionId}/library/{book}/download', [PublicController::class, 'download'])->name('download');
    
    // Update reading progress (NEW)
    Route::post('/{institutionId}/library/{book}/progress', [PublicController::class, 'updateProgress'])->name('progress');
    
    // Shelf books
    Route::get('/{institutionId}/shelf/{shelfId}', [PublicController::class, 'shelfShow'])->name('shelf.show');
    
    // Category filter
    Route::get('/{institutionId}/category/{category}', [PublicController::class, 'category'])->name('category');
    
    // Featured books
    Route::get('/{institutionId}/featured', [PublicController::class, 'featured'])->name('featured');
    
    // Trending books
    Route::get('/{institutionId}/trending', [PublicController::class, 'trending'])->name('trending');
    
    // Search
    Route::get('/{institutionId}/search', [PublicController::class, 'search'])->name('search');
});

// Public library
Route::get('/library', [LibraryController::class, 'index'])->name('library.index');
Route::get('/library/{id}', [LibraryController::class, 'show'])->name('library.show');
Route::get('/library/download/raw/{bookId}', [LibraryController::class, 'downloadRaw'])->name('library.download.raw');

// Referral
Route::get('/refer/{code}', [ReferralController::class, 'processReferral'])->name('referral.process');

// Certificate serving
Route::get('/certificates/serve/{id}', [CertificateController::class, 'serve'])->name('certificates.serve');

// Public profile
Route::get('/@{username}', [ProfileController::class, 'show'])->name('profile.show');


// ==========================================
// SECTION 3: PAYMENT WEBHOOKS (Public callbacks)
// ==========================================

Route::post('/webhooks/mpesa', [PaymentWebhookController::class, 'handleMpesaCallback']);
Route::post('/webhooks/tigopesa', [PaymentWebhookController::class, 'handleTigopesaCallback']);
Route::post('/webhooks/halopesa', [PaymentWebhookController::class, 'handleHalopesaCallback']);
Route::post('/webhooks/stripe', [PaymentWebhookController::class, 'handleStripeWebhook']);
Route::post('/webhooks/pesapal', [PaymentWebhookController::class, 'handlePesapalCallback']);
Route::get('/payment/pesapal/callback', [MultiPaymentController::class, 'pesapalCallback'])->name('payment.pesapal.callback');


// Institution subscription webhooks
Route::post('/webhooks/mpesa/callback', [InstitutionSubscriptionController::class, 'mpesaCallback'])->name('mpesa.callback');
Route::post('/webhooks/tigopesa/callback', [InstitutionSubscriptionController::class, 'tigopesaCallback'])->name('tigopesa.callback');
Route::post('/webhooks/halopesa/callback', [InstitutionSubscriptionController::class, 'halopesaCallback'])->name('halopesa.callback');
Route::get('/webhooks/pesapal/callback', [InstitutionSubscriptionController::class, 'pesapalCallback'])->name('pesapal.callback');


// ==========================================
// SECTION 4: AUTHENTICATED ROUTES (General Users)
// ==========================================

Route::middleware(['auth'])->group(function () {

    // ---- Dashboard & Leaderboard ----
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/leaderboard', [LeaderboardController::class, 'index'])->name('leaderboard.index');
    Route::get('/leaderboard/data', [LeaderboardController::class, 'index'])->name('leaderboard.data');

    // ---- Invoices ----
    Route::prefix('invoices')->name('invoices.')->group(function () {
        Route::get('/payment/{paymentId}', [InvoiceController::class, 'paymentInvoice'])->name('payment');
        Route::get('/transaction/{transactionId}', [InvoiceController::class, 'transactionInvoice'])->name('transaction');
        Route::get('/subscription/{subscriptionPaymentId}', [InvoiceController::class, 'subscriptionInvoice'])->name('subscription');
    });

    // ---- Applications (Author, etc.) ----
  Route::get('/apply/{type}', [App\Http\Controllers\ApplicationController::class, 'create'])->name('applications.create');
    Route::post('/applications', [App\Http\Controllers\ApplicationController::class, 'store'])->name('applications.store');

    // ---- Institutions (User Side) ----
    Route::get('/my-institution', [InstitutionController::class, 'myInstitution'])->name('my.institution');
    Route::get('/discover-institutions', [InstitutionController::class, 'discover'])->name('discover.institutions');
    Route::get('/institutions/{id}', [InstitutionController::class, 'show'])->name('institutions.show');
    Route::post('/institution/leave/{institutionId?}', [InstitutionController::class, 'leave'])->name('institution.leave');
    Route::post('/institution/free-join/{id}', [InstitutionController::class, 'freeJoin'])->name('institution.join.free');

    // ---- Institution Creation Requests ----
    Route::prefix('institution')->name('institution.')->group(function () {
        Route::get('/create-request', [InstitutionCreationController::class, 'create'])->name('create-request');
        Route::post('/create-request', [InstitutionCreationController::class, 'store'])->name('store-request');
        Route::get('/my-requests', [InstitutionCreationController::class, 'myRequests'])->name('my-requests');
        Route::get('/request/{id}', [InstitutionCreationController::class, 'show'])->name('request.show');
        Route::post('/request/{id}/cancel', [InstitutionCreationController::class, 'cancel'])->name('request.cancel');
    });

    // ---- Join Requests ----
    Route::prefix('join-requests')->name('join-requests.')->group(function () {
        Route::get('/', [JoinRequestController::class, 'myRequests'])->name('index');
        Route::post('/', [JoinRequestController::class, 'store'])->name('store');
        Route::delete('/{joinRequest}', [JoinRequestController::class, 'cancel'])->name('cancel');
    });

    // ---- Global Search ----
    Route::get('/api/global-search', [GlobalSearchController::class, 'api'])->name('global.search.api');
    Route::get('/global-search', [GlobalSearchController::class, 'index'])->name('global.search');

    // ---- Wallet ----
    Route::prefix('wallet')->name('wallet.')->group(function () {
        Route::get('/', [WalletController::class, 'index'])->name('index');
        Route::get('/balance', [WalletController::class, 'getBalance'])->name('balance');
        Route::post('/withdraw', [WalletController::class, 'withdraw'])->name('withdraw');
        Route::post('/topup', [WalletController::class, 'topUp'])->name('topup');
        Route::get('/export', [WalletController::class, 'exportTransactions'])->name('export');
    });

    // ---- Author Wallet (Legacy) ----
    Route::prefix('author')->name('author.')->group(function () {
        Route::get('/wallet', [AuthorWalletController::class, 'index'])->name('wallet');
        Route::post('/wallet/withdraw', [AuthorWalletController::class, 'requestWithdrawal'])->name('wallet.withdraw');
    });

    // ---- User Subscriptions ----
    Route::prefix('user')->name('user.')->group(function () {
        Route::get('/subscription', [UserSubscriptionController::class, 'index'])->name('subscription.index');
        Route::get('/subscription/history', [UserSubscriptionController::class, 'history'])->name('subscription.history');
        Route::post('/subscription/extend', [UserSubscriptionController::class, 'extend'])->name('subscription.extend');
        Route::post('/subscription/cancel', [UserSubscriptionController::class, 'cancel'])->name('subscription.cancel');
    });

    // ---- Library (User) ----
    Route::prefix('library')->name('library.')->group(function () {
        Route::get('/', [LibraryController::class, 'index'])->name('index');
        Route::get('/my-library', [LibraryController::class, 'myLibrary'])->name('my-library');
        Route::get('/{book}', [LibraryController::class, 'show'])->name('show');
        Route::get('/{book}/read', [LibraryController::class, 'read'])->name('read');
        Route::get('/{book}/pdf', [LibraryController::class, 'servePdf'])->name('pdf');
        Route::get('/{book}/download', [LibraryController::class, 'download'])->name('download');
        Route::post('/{book}/progress', [LibraryController::class, 'updateProgress'])->name('progress');
        Route::post('/{book}/add-to-library', [LibraryController::class, 'addToLibrary'])->name('add-to-library');
    });

    // ---- User Personal Library ----
    Route::prefix('user/library')->name('user.library.')->group(function () {
        Route::get('/', [UserLibraryController::class, 'index'])->name('index');
        Route::get('/{bookId}', [UserLibraryController::class, 'show'])->name('show');
        Route::post('/{bookId}/progress', [UserLibraryController::class, 'updateProgress'])->name('progress');
        Route::post('/{bookId}/track', [UserLibraryController::class, 'trackView'])->name('track');
    });

    // ---- My Borrowings ----
    Route::get('/my-borrowings', [LibrarianBorrowingController::class, 'myBorrowings'])->name('my.borrowings');

    // ---- Payments ----
    Route::prefix('payment')->name('payment.')->group(function () {
        Route::get('/methods', [MultiPaymentController::class, 'showMethods'])->name('methods');
        Route::post('/initiate', [MultiPaymentController::class, 'initiatePayment'])->name('initiate');
        Route::post('/save-details', [MultiPaymentController::class, 'savePaymentDetails'])->name('save-details');
        Route::get('/status/{paymentId}', [MultiPaymentController::class, 'checkStatus'])->name('status');
    });

    // ---- Referrals ----
    Route::prefix('referrals')->name('referrals.')->group(function () {
        Route::get('/', [ReferralController::class, 'index'])->name('index');
        Route::post('/{id}/complete', [ReferralController::class, 'markComplete'])->name('complete');
    });

    // ---- Search ----
    Route::prefix('search')->name('search.')->group(function () {
        Route::get('/', [SearchController::class, 'index'])->name('index');
        Route::get('/live', [SearchController::class, 'live'])->name('live');
        Route::get('/filter', [SearchController::class, 'filter'])->name('filter');
    });

    // ---- Notifications ----
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::get('/unread-count', [NotificationController::class, 'getUnreadCount'])->name('unread-count');
        Route::get('/latest', [NotificationController::class, 'getLatest'])->name('latest');
        Route::post('/mark-all-read', [NotificationController::class, 'markAllRead'])->name('mark-all-read');
        Route::post('/{id}/read', [NotificationController::class, 'markAsRead'])->name('mark-read');
        Route::delete('/{id}', [NotificationController::class, 'destroy'])->name('destroy');
    });

    // ---- Bookmarks ----
    Route::post('/bookmark/toggle', [BookmarkController::class, 'toggle'])->name('bookmark.toggle');
    Route::get('/bookmarks', [BookmarkController::class, 'index'])->name('bookmarks.index');
    Route::delete('/bookmarks/{id}', [BookmarkController::class, 'destroy'])->name('bookmark.destroy');

    // ---- Ratings & Reviews ----
    Route::post('/books/{book}/rate', [RatingReviewController::class, 'rate'])->name('books.rate');
    Route::post('/books/{book}/review', [RatingReviewController::class, 'review'])->name('books.review');
    Route::post('/reviews/{review}/helpful', [RatingReviewController::class, 'helpful'])->name('reviews.helpful');
    Route::delete('/books/{book}/rating', [RatingReviewController::class, 'deleteRating'])->name('books.rating.delete');
    Route::delete('/books/{book}/review', [RatingReviewController::class, 'deleteReview'])->name('books.review.delete');

    // ---- Quizzes ----
    Route::get('/quizzes', [QuizController::class, 'index'])->name('quizzes.index');
    Route::get('/quizzes/{id}', [QuizController::class, 'show'])->name('quizzes.show');
    Route::post('/quizzes/{id}/submit', [QuizController::class, 'submit'])->name('quizzes.submit');
    Route::get('/quizzes/results/{attemptId}', [QuizController::class, 'results'])->name('quizzes.results');
    Route::get('/quizzes/history', [QuizController::class, 'history'])->name('quizzes.history');

    // ---- Profile ----
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
        Route::put('/update', [ProfileController::class, 'update'])->name('update');
        Route::post('/avatar', [ProfileController::class, 'updateAvatar'])->name('avatar');
        Route::post('/cover', [ProfileController::class, 'updateCover'])->name('cover');
        Route::delete('/avatar', [ProfileController::class, 'deleteAvatar'])->name('avatar.delete');
        Route::delete('/cover', [ProfileController::class, 'deleteCover'])->name('cover.delete');
    });

    // ---- AI Chat ----
    Route::prefix('ai')->name('ai.')->group(function () {
        Route::get('/chat', [AIController::class, 'index'])->name('chat');
        Route::get('/chat/{chat_session}', [AIController::class, 'index'])->name('chat.session');
        Route::post('/send', [AIController::class, 'sendMessage'])->name('send');
        Route::post('/new-session', [AIController::class, 'newSession'])->name('new');
        Route::delete('/session/{id}', [AIController::class, 'deleteSession'])->name('delete');
        Route::get('/session/{id}', [AIController::class, 'getSession'])->name('get');
    });

    // ---- Certificates ----
    Route::prefix('certificates')->name('certificates.')->group(function () {
        Route::get('/', [CertificateController::class, 'index'])->name('index');
        Route::get('/generate/{book}', [CertificateController::class, 'generate'])->name('generate');
        Route::get('/show/{certificate}', [CertificateController::class, 'show'])->name('show');
        Route::get('/download/{certificate}', [CertificateController::class, 'download'])->name('download');
        Route::get('/fix-missing', [CertificateController::class, 'fixMissingCertificates'])->name('fix-missing');
    });

    // ---- Community ----
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

    // ---- File Converter ----
    Route::prefix('converter')->name('converter.')->group(function () {
        Route::get('/', [ConverterController::class, 'index'])->name('index');
        Route::post('/pdf-to-word', [ConverterController::class, 'pdfToWord'])->name('pdf-to-word');
        Route::post('/word-to-pdf', [ConverterController::class, 'wordToPdf'])->name('word-to-pdf');
        Route::post('/book-to-audio', [ConverterController::class, 'bookToAudio'])->name('book-to-audio');
    });

    // ---- Documents ----
    Route::prefix('documents')->name('documents.')->group(function () {
        Route::get('/', [DocumentController::class, 'index'])->name('index');
        Route::get('/create', [DocumentController::class, 'create'])->name('create');
        Route::post('/upload', [DocumentController::class, 'upload'])->name('upload');
        Route::get('/{document}', [DocumentController::class, 'show'])->name('show');
        Route::post('/{document}/ask', [DocumentController::class, 'ask'])->name('ask');
        Route::delete('/{document}', [DocumentController::class, 'destroy'])->name('destroy');
        Route::get('/{document}/chat', [DocumentController::class, 'chat'])->name('chat');
    });

    // ---- Institution Members Directory ----
    Route::get('/institution/members/directory', [InstitutionMemberController::class, 'directory'])->name('institution.members.directory');

    // ---- Withdrawals (General) ----
    Route::prefix('withdrawals')->name('withdrawals.')->group(function () {
        Route::get('/', [App\Http\Controllers\WithdrawalController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\WithdrawalController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\WithdrawalController::class, 'store'])->name('store');
        Route::get('/{withdrawal}', [App\Http\Controllers\WithdrawalController::class, 'show'])->name('show');
        Route::post('/{withdrawal}/cancel', [App\Http\Controllers\WithdrawalController::class, 'cancel'])->name('cancel');
    });

    // ---- Quotes API ----
    Route::get('/api/quote-of-the-day', [QuoteController::class, 'quoteOfTheDay']);
    Route::post('/api/quote/{quote}/favorite', [QuoteController::class, 'toggleFavorite']);
    Route::post('/api/quote/{quote}/share', [QuoteController::class, 'share']);
    Route::get('/api/quote/next', [QuoteController::class, 'nextQuote']);

    // ---- Wallet API ----
    Route::get('/api/user/wallet-balance', function() {
        if (!auth()->check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        return response()->json([
            'balance' => auth()->user()->wallet_balance ?? 0,
            'currency' => 'TSh'
        ]);
    })->name('api.wallet.balance');

    // ---- Shelf Sync ----
    Route::post('/shelves/sync-counts', [InstitutionShelfController::class, 'syncCounts'])->name('institution.shelves.sync-counts');
});


// ==========================================
// SECTION 5: BORROW REQUESTS
// ==========================================

Route::middleware(['auth'])->group(function () {
    Route::get('/borrow/request/{book_id}/{institution_id}', [BorrowRequestController::class, 'create'])->name('borrow.request.form');
    Route::post('/borrow/request', [BorrowRequestController::class, 'store'])->name('borrow.request.store');
});

Route::middleware(['auth', 'librarian'])->prefix('librarian')->group(function () {
    Route::get('/borrow-requests', [BorrowRequestController::class, 'index'])->name('librarian.borrow-requests.index');
    Route::post('/borrow-requests/{id}/approve', [BorrowRequestController::class, 'approve'])->name('librarian.borrow-requests.approve');
    Route::post('/borrow-requests/{id}/reject', [BorrowRequestController::class, 'reject'])->name('librarian.borrow-requests.reject');
});

// ==========================================
// SECTION 6: BOOK PURCHASES
// ==========================================

Route::middleware(['auth'])->group(function () {
    Route::get('/book/purchase/{bookId}', [BookPurchaseController::class, 'purchase'])->name('book.purchase');
    Route::post('/book/purchase/process', [BookPurchaseController::class, 'processPurchase'])->name('book.purchase.process');
    Route::get('/book/purchase/success/{paymentId}', [BookPurchaseController::class, 'purchaseSuccess'])->name('book.purchase.success');
    Route::get('/book/purchase/history', [BookPurchaseController::class, 'purchaseHistory'])->name('book.purchase.history');
    Route::get('/book/download/{bookId}', [BookPurchaseController::class, 'downloadBook'])->name('book.download');
});


Route::get('/book-purchase/pesapal/callback/{paymentId}', [BookPurchaseController::class, 'pesapalCallback'])->name('book.purchase.pesapal.callback');
Route::get('/book-purchase/pesapal/ipn', [BookPurchaseController::class, 'pesapalIpn'])->name('book.purchase.pesapal.ipn');
Route::get('/book-purchase/pesapal/status/{paymentId}', [BookPurchaseController::class, 'checkPesapalStatus'])->name('book.purchase.pesapal.status');

// ==========================================
// SECTION 7: INSTITUTION LIBRARY (Public + Auth)
// ==========================================

Route::prefix('institution')->middleware(['auth'])->group(function () {
    Route::post('/books/{book}/progress', [InstitutionBookController::class, 'updateProgress'])->name('institution.books.progress');
});

Route::middleware(['auth', 'institution'])->prefix('institution')->name('institution.')->group(function () {
    Route::get('/books', [InstitutionLibraryController::class, 'index'])->name('books.index');
    Route::get('/books/{book}', [InstitutionLibraryController::class, 'show'])->name('books.show');
    Route::get('/books/shelf/{shelfId}/books', [InstitutionLibraryController::class, 'getShelfBooks'])->name('books.shelf.books');
    Route::get('/books/stats', [InstitutionLibraryController::class, 'getStats'])->name('books.stats');
});

// Institution Orders
Route::middleware(['auth'])->group(function () {
    Route::get('/orders/create/{book}', [InstitutionOrderController::class, 'create'])->name('orders.create');
    Route::post('/orders', [InstitutionOrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/{order}', [InstitutionOrderController::class, 'show'])->name('orders.show');
});

Route::middleware(['auth', 'institution'])->prefix('institution')->name('institution.')->group(function () {
    Route::get('/orders', [InstitutionOrderController::class, 'index'])->name('orders.index');
    Route::post('/orders/{order}/status', [InstitutionOrderController::class, 'updateStatus'])->name('orders.update-status');
});

// Institution Subscription Payments
Route::middleware(['auth', 'institution'])->prefix('institution')->name('institution.subscription.')->group(function () {
    Route::post('/initiate-payment', [InstitutionSubscriptionController::class, 'initiatePayment'])->name('initiate-payment');
    Route::get('/payment-status/{subscriptionId}', [InstitutionSubscriptionController::class, 'paymentStatus'])->name('payment-status');
    Route::get('/payment-instructions/{subscriptionId}', [InstitutionSubscriptionController::class, 'paymentInstructions'])->name('payment-instructions');
});


// ==========================================
// SECTION 8: AUTHOR & SELLER STUDIO (Merged)
// ==========================================

Route::middleware(['auth', 'author.seller'])->prefix('studio')->name('author.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [AuthorDashboardController::class, 'index'])->name('dashboard');

    // Books
    Route::resource('books', AuthorBookController::class);

    // Earnings
    Route::get('/earnings', [EarningController::class, 'index'])->name('earnings');

    // Withdrawals
    Route::prefix('withdrawals')->name('withdrawals.')->group(function () {
        Route::get('/', [WithdrawalController::class, 'index'])->name('index');
        Route::get('/create', [WithdrawalController::class, 'create'])->name('create');
        Route::post('/', [WithdrawalController::class, 'store'])->name('store');
    });

    // Royalties
    Route::get('/royalties', [RoyaltyController::class, 'index'])->name('royalties.index');
});

// Seller routes (shared with author)
Route::middleware(['auth', 'author.seller'])->prefix('seller')->name('seller.')->group(function () {
    Route::get('/listings', [SellerListingController::class, 'index'])->name('listings');
    Route::get('/listings/create', [SellerListingController::class, 'create'])->name('listings.create');
    Route::get('/orders', [SellerOrderController::class, 'index'])->name('orders');
    Route::get('/orders/{order}', [SellerOrderController::class, 'show'])->name('orders.show');
    Route::put('/orders/{order}/status', [SellerOrderController::class, 'updateStatus'])->name('orders.status');
});

// Marketplace
Route::middleware(['auth', 'author.seller'])->prefix('marketplace')->name('marketplace.')->group(function () {
    Route::get('/create', [MarketplaceController::class, 'create'])->name('create');
    Route::post('/store', [MarketplaceController::class, 'store'])->name('store');
    Route::get('/{listing}/edit', [MarketplaceController::class, 'edit'])->name('edit');
    Route::put('/{listing}', [MarketplaceController::class, 'update'])->name('update');
    Route::delete('/{listing}', [MarketplaceController::class, 'destroy'])->name('destroy');
    Route::get('/listings', [MarketplaceController::class, 'myListings'])->name('listings');
});
// ==========================================
// SECTION 9: INSTRUCTOR ROUTES
// ==========================================

Route::middleware(['auth'])->prefix('instructor')->name('instructor.')->group(function () {
    Route::get('/dashboard', [InstructorDashboardController::class, 'index'])->name('dashboard');
    Route::resource('courses', InstructorCourseController::class);
    Route::post('/courses/{course}/lessons', [InstructorCourseController::class, 'addLesson'])->name('courses.lessons.store');
    Route::put('/lessons/{lesson}', [InstructorCourseController::class, 'updateLesson'])->name('lessons.update');
    Route::delete('/lessons/{lesson}', [InstructorCourseController::class, 'deleteLesson'])->name('lessons.destroy');
    Route::get('/courses/{course}/enrollments', [InstructorCourseController::class, 'enrollments'])->name('courses.enrollments');
});


// ==========================================
// SECTION 10: INSTITUTION ADMIN ROUTES
// ==========================================

Route::middleware(['auth', 'institution'])->prefix('institution')->name('institution.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [InstitutionDashboardController::class, 'index'])->name('dashboard');

    // Books
    Route::resource('books', InstitutionBookController::class);
    Route::post('/books/{book}/approve', [InstitutionBookController::class, 'approve'])->name('books.approve');
    Route::post('/books/{book}/toggle-stock', [InstitutionBookController::class, 'toggleStock'])->name('books.toggle-stock');
    Route::post('/books/bulk-action', [InstitutionBookController::class, 'bulkAction'])->name('books.bulk-action');

    // Shelves
    Route::resource('shelves', InstitutionShelfController::class);

    // Members
    Route::resource('members', InstitutionMemberController::class);
    Route::post('/members/{member}/role', [InstitutionMemberController::class, 'updateRole'])->name('members.update-role');
    Route::get('/members/export', [InstitutionMemberController::class, 'export'])->name('members.export');
    Route::post('/members/bulk-action', [InstitutionMemberController::class, 'bulkAction'])->name('members.bulk-action');
    Route::get('/members/{id}/json', [InstitutionMemberController::class, 'editJson'])->name('members.json');
    Route::get('/members/trashed', [InstitutionMemberController::class, 'trashed'])->name('members.trashed');
    Route::post('/members/{id}/restore', [InstitutionMemberController::class, 'restore'])->name('members.restore');
    Route::delete('/members/{id}/force-delete', [InstitutionMemberController::class, 'forceDelete'])->name('members.force-delete');

    // Join Requests
    Route::get('/join-requests', [InstitutionJoinRequestController::class, 'index'])->name('join-requests.index');
    Route::get('/join-requests/{id}', [InstitutionJoinRequestController::class, 'show'])->name('join-requests.show');
    Route::post('/join-requests/{id}/approve', [InstitutionJoinRequestController::class, 'approve'])->name('join-requests.approve');
    Route::post('/join-requests/{id}/reject', [InstitutionJoinRequestController::class, 'reject'])->name('join-requests.reject');

    // Borrowings
    Route::get('/borrowings', [InstitutionBorrowingController::class, 'index'])->name('borrowings.index');
    Route::get('/borrowings/create', [InstitutionBorrowingController::class, 'create'])->name('borrowings.create');
    Route::post('/borrowings', [InstitutionBorrowingController::class, 'store'])->name('borrowings.store');
    Route::get('/borrowings/{borrowing}', [InstitutionBorrowingController::class, 'show'])->name('borrowings.show');
    Route::post('/borrowings/{borrowing}/return', [InstitutionBorrowingController::class, 'returnBook'])->name('borrowings.return');
    Route::delete('/borrowings/{borrowing}', [InstitutionBorrowingController::class, 'destroy'])->name('borrowings.destroy');

    // Withdrawals
    Route::resource('withdrawals', InstitutionWithdrawalController::class);
    Route::post('/withdrawals/{withdrawal}/cancel', [InstitutionWithdrawalController::class, 'cancel'])->name('withdrawals.cancel');
    Route::get('/withdrawals/export', [InstitutionWithdrawalController::class, 'export'])->name('withdrawals.export');

    // Reports
    Route::get('/reports', [InstitutionReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [InstitutionReportController::class, 'export'])->name('reports.export');

    // Settings
    Route::get('/settings', [InstitutionSettingController::class, 'index'])->name('settings');
    Route::put('/settings', [InstitutionSettingController::class, 'update'])->name('settings.update');

    // Subscription
    Route::get('/subscription', [InstitutionSubscriptionController::class, 'index'])->name('subscription.index');
    Route::post('/subscription/extend', [InstitutionSubscriptionController::class, 'extend'])->name('subscription.extend');
    Route::get('/subscription/history', [InstitutionSubscriptionController::class, 'history'])->name('subscription.history');
    Route::post('/subscription/cancel', [InstitutionSubscriptionController::class, 'cancel'])->name('subscription.cancel');
});


// ==========================================
// SECTION 11: LIBRARIAN ROUTES
// ==========================================

Route::middleware(['auth', LibrarianMiddleware::class])->prefix('librarian')->name('librarian.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [LibrarianDashboardController::class, 'index'])->name('dashboard');

    // Books
    Route::resource('books', LibrarianBookController::class);
    Route::post('/books/{book}/approve', [LibrarianBookController::class, 'approve'])->name('books.approve');
    Route::post('/books/{book}/reject', [LibrarianBookController::class, 'reject'])->name('books.reject');

    // Shelves
    Route::resource('shelves', LibrarianShelfController::class);

    // Members
    Route::get('/members', [LibrarianMemberController::class, 'index'])->name('members.index');
    Route::get('/members/{member}', [LibrarianMemberController::class, 'show'])->name('members.show');
    Route::post('/members/{member}/role', [LibrarianMemberController::class, 'updateRole'])->name('members.update-role');
    Route::delete('/members/{member}', [LibrarianMemberController::class, 'destroy'])->name('members.destroy');
    Route::get('/members/export', [LibrarianMemberController::class, 'export'])->name('members.export');

    // Reports
    Route::get('/reports', [LibrarianReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [LibrarianReportController::class, 'export'])->name('reports.export');

    // Join Requests
    Route::get('/join-requests', [LibrarianJoinRequestController::class, 'index'])->name('join-requests');
    Route::post('/join-requests/{joinRequest}/approve', [LibrarianJoinRequestController::class, 'approve'])->name('join-requests.approve');
    Route::post('/join-requests/{joinRequest}/reject', [LibrarianJoinRequestController::class, 'reject'])->name('join-requests.reject');

    // Settings
    Route::get('/settings', [LibrarianSettingController::class, 'index'])->name('settings');
    Route::put('/settings', [LibrarianSettingController::class, 'update'])->name('settings.update');

    // Borrowings
    Route::get('/borrowings', [LibrarianBorrowingController::class, 'index'])->name('borrowings.index');
    Route::get('/borrowings/create', [LibrarianBorrowingController::class, 'create'])->name('borrowings.create');
    Route::post('/borrowings', [LibrarianBorrowingController::class, 'store'])->name('borrowings.store');
    Route::get('/borrowings/{borrowing}', [LibrarianBorrowingController::class, 'show'])->name('borrowings.show');
    Route::post('/borrowings/{borrowing}/return', [LibrarianBorrowingController::class, 'returnBook'])->name('borrowings.return');
    Route::delete('/borrowings/{borrowing}', [LibrarianBorrowingController::class, 'destroy'])->name('borrowings.destroy');
});


// ==========================================
// SECTION 12: ADMIN ROUTES
// ==========================================

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard & Analytics
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics');
    Route::get('/analytics/data', [AnalyticsController::class, 'getData'])->name('analytics.data');

    // Books
    Route::resource('books', AdminBookController::class);
    Route::post('/books/{book}/toggle-status', [AdminBookController::class, 'toggleStatus'])->name('books.toggle-status');
    Route::post('/books/{book}/toggle-featured', [AdminBookController::class, 'toggleFeatured'])->name('books.toggle-featured');
    Route::post('/books/{book}/toggle-trending', [AdminBookController::class, 'toggleTrending'])->name('books.toggle-trending');
    Route::post('/books/bulk-action', [AdminBookController::class, 'bulkAction'])->name('books.bulk-action');

    // Users
    Route::resource('users', AdminUserController::class);
    Route::post('/users/{user}/toggle-role', [AdminUserController::class, 'toggleRole'])->name('users.toggle-role');

    // Institutions
    Route::resource('institutions', App\Http\Controllers\Admin\InstitutionController::class);
    Route::post('/institutions/{institution}/approve', [App\Http\Controllers\Admin\InstitutionController::class, 'approve'])->name('institutions.approve');
    Route::post('/institutions/{institution}/reject', [App\Http\Controllers\Admin\InstitutionController::class, 'reject'])->name('institutions.reject');
    Route::post('/institutions/{institution}/suspend', [App\Http\Controllers\Admin\InstitutionController::class, 'suspend'])->name('institutions.suspend');
    Route::get('/institutions/{institution}/members', [App\Http\Controllers\Admin\InstitutionMemberController::class, 'index'])->name('institutions.members');
    Route::get('/institutions/{institution}/members/create', [App\Http\Controllers\Admin\InstitutionMemberController::class, 'create'])->name('institutions.members.create');
    Route::post('/institutions/{institution}/members', [App\Http\Controllers\Admin\InstitutionMemberController::class, 'store'])->name('institutions.members.store');
    Route::delete('/institutions/{institution}/members/{member}', [App\Http\Controllers\Admin\InstitutionMemberController::class, 'destroy'])->name('institutions.members.destroy');

    // Marketplace
    Route::get('/marketplace/pending', [AdminMarketplaceController::class, 'pending'])->name('marketplace.pending');
    Route::get('/marketplace/all', [AdminMarketplaceController::class, 'all'])->name('marketplace.all');
    Route::post('/marketplace/{listing}/approve', [AdminMarketplaceController::class, 'approve'])->name('marketplace.approve');
    Route::post('/marketplace/{listing}/reject', [AdminMarketplaceController::class, 'reject'])->name('marketplace.reject');

    // Applications
    Route::get('/applications', [AdminApplicationController::class, 'index'])->name('applications.index');
    Route::get('/applications/{application}', [AdminApplicationController::class, 'show'])->name('applications.show');
    Route::post('/applications/{application}/approve', [AdminApplicationController::class, 'approve'])->name('applications.approve');
    Route::post('/applications/{application}/reject', [AdminApplicationController::class, 'reject'])->name('applications.reject');
    Route::delete('/applications/{application}', [AdminApplicationController::class, 'destroy'])->name('applications.destroy');

    // Payments
    Route::prefix('payments')->name('payments.')->group(function () {
        Route::get('/', [AdminPaymentController::class, 'index'])->name('index');
        Route::get('/transactions', [AdminPaymentController::class, 'transactions'])->name('transactions');
        Route::get('/commissions', [AdminPaymentController::class, 'commissions'])->name('commissions');
        Route::get('/author-payouts', [AdminPaymentController::class, 'authorPayouts'])->name('author-payouts');
        Route::get('/audit-logs', [AdminPaymentController::class, 'auditLogs'])->name('audit-logs');
        Route::get('/{id}', [AdminPaymentController::class, 'show'])->name('show');
    });

    // Quotes
    Route::get('/quotes', [QuoteController::class, 'index'])->name('quotes.index');
    Route::get('/quotes/create', [QuoteController::class, 'create'])->name('quotes.create');
    Route::post('/quotes', [QuoteController::class, 'store'])->name('quotes.store');
    Route::get('/quotes/{quote}/edit', [QuoteController::class, 'edit'])->name('quotes.edit');
    Route::put('/quotes/{quote}', [QuoteController::class, 'update'])->name('quotes.update');
    Route::delete('/quotes/{quote}', [QuoteController::class, 'destroy'])->name('quotes.destroy');
    Route::get('/quotes/{quote}/analytics', [QuoteController::class, 'analytics'])->name('quotes.analytics');
});


// ==========================================
// SECTION 13: SUPER ADMIN ROUTES
// ==========================================

Route::middleware(['auth', 'superadmin'])->prefix('super-admin')->name('super-admin.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [SuperAdminDashboardController::class, 'index'])->name('dashboard');

    // Analytics
    Route::get('/analytics', [SuperAdminAnalyticsController::class, 'index'])->name('analytics.index');
    Route::get('/analytics/data', [SuperAdminAnalyticsController::class, 'getData'])->name('analytics.data');
    Route::get('/analytics/export', [SuperAdminAnalyticsController::class, 'export'])->name('analytics.export');

    // Books
    Route::resource('books', App\Http\Controllers\SuperAdmin\BookController::class);
    Route::post('/books/{book}/toggle-status', [App\Http\Controllers\SuperAdmin\BookController::class, 'toggleStatus'])->name('books.toggle-status');
    Route::post('/books/{book}/toggle-featured', [App\Http\Controllers\SuperAdmin\BookController::class, 'toggleFeatured'])->name('books.toggle-featured');
    Route::post('/books/{book}/toggle-trending', [App\Http\Controllers\SuperAdmin\BookController::class, 'toggleTrending'])->name('books.toggle-trending');
    Route::post('/books/bulk-action', [App\Http\Controllers\SuperAdmin\BookController::class, 'bulkAction'])->name('books.bulk-action');

    // Users
    Route::resource('users', App\Http\Controllers\SuperAdmin\UserController::class);

    // Institutions
    Route::resource('institutions', App\Http\Controllers\SuperAdmin\InstitutionController::class);
    Route::post('/institutions/{institution}/approve', [App\Http\Controllers\SuperAdmin\InstitutionController::class, 'approve'])->name('institutions.approve');
    Route::post('/institutions/{institution}/suspend', [App\Http\Controllers\SuperAdmin\InstitutionController::class, 'suspend'])->name('institutions.suspend');
    Route::post('/institutions/{institution}/reject', [App\Http\Controllers\SuperAdmin\InstitutionController::class, 'reject'])->name('institutions.reject');
    Route::post('/institutions/{institution}/activate-subscription', [App\Http\Controllers\SuperAdmin\InstitutionController::class, 'activateSubscription'])->name('institutions.activate-subscription');
    Route::get('/institutions/{institution}/subscription-override', function ($institution) {
        $institution = \App\Models\Institution::findOrFail($institution);
        return view('super-admin.institutions.subscription-override', compact('institution'));
    })->name('institutions.subscription-override');

    // Institution Requests
    Route::get('/institution-requests', [InstitutionRequestController::class, 'index'])->name('institution-requests.index');
    Route::get('/institution-requests/{id}', [InstitutionRequestController::class, 'show'])->name('institution-requests.show');
    Route::post('/institution-requests/{id}/approve', [InstitutionRequestController::class, 'approve'])->name('institution-requests.approve');
    Route::post('/institution-requests/{id}/reject', [InstitutionRequestController::class, 'reject'])->name('institution-requests.reject');
    Route::get('/institution-requests/{id}/download', [InstitutionRequestController::class, 'download'])->name('institution-requests.download');

    // Institution Subscriptions
    Route::prefix('institution-subscriptions')->name('institutions.subscriptions.')->group(function () {
        Route::get('/', [SuperAdminInstitutionSubscriptionController::class, 'index'])->name('index');
        Route::get('/{id}', [SuperAdminInstitutionSubscriptionController::class, 'show'])->name('show');
        Route::post('/{id}/assign', [SuperAdminInstitutionSubscriptionController::class, 'assign'])->name('assign');
        Route::post('/bulk', [SuperAdminInstitutionSubscriptionController::class, 'bulkAssign'])->name('bulk');
        Route::post('/subscription/{id}/activate', [SuperAdminInstitutionSubscriptionController::class, 'activate'])->name('activate');
        Route::post('/subscription/{id}/cancel', [SuperAdminInstitutionSubscriptionController::class, 'cancel'])->name('cancel');
        Route::delete('/subscription/{id}', [SuperAdminInstitutionSubscriptionController::class, 'destroy'])->name('destroy');
        Route::get('/export', [SuperAdminInstitutionSubscriptionController::class, 'export'])->name('export');
    });

    // Marketplace
    Route::prefix('marketplace')->name('marketplace.')->group(function () {
        Route::get('/', [App\Http\Controllers\SuperAdmin\MarketplaceController::class, 'index'])->name('index');
        Route::get('/{listing}', [App\Http\Controllers\SuperAdmin\MarketplaceController::class, 'show'])->name('show');
        Route::post('/{listing}/approve', [App\Http\Controllers\SuperAdmin\MarketplaceController::class, 'approve'])->name('approve');
        Route::post('/{listing}/reject', [App\Http\Controllers\SuperAdmin\MarketplaceController::class, 'reject'])->name('reject');
        Route::delete('/{listing}', [App\Http\Controllers\SuperAdmin\MarketplaceController::class, 'destroy'])->name('destroy');
    });

    // Applications
    Route::prefix('applications')->name('applications.')->group(function () {
        Route::get('/', [SuperAdminApplicationController::class, 'index'])->name('index');
        Route::get('/{application}', [SuperAdminApplicationController::class, 'show'])->name('show');
        Route::post('/{application}/approve', [SuperAdminApplicationController::class, 'approve'])->name('approve');
        Route::post('/{application}/reject', [SuperAdminApplicationController::class, 'reject'])->name('reject');
        Route::get('/{application}/download/{document}', [SuperAdminApplicationController::class, 'download'])->name('download');
        Route::delete('/{application}', [SuperAdminApplicationController::class, 'destroy'])->name('destroy');
    });

    // Payments
    Route::prefix('payments')->name('payments.')->group(function () {
        Route::get('/', [SuperAdminPaymentController::class, 'index'])->name('index');
        Route::get('/transactions', [SuperAdminPaymentController::class, 'transactions'])->name('transactions');
        Route::get('/withdrawals', [SuperAdminPaymentController::class, 'withdrawals'])->name('withdrawals');
        Route::get('/commissions', [SuperAdminPaymentController::class, 'commissions'])->name('commissions');
        Route::get('/author-payouts', [SuperAdminPaymentController::class, 'authorPayouts'])->name('author-payouts');
        Route::get('/audit-logs', [SuperAdminPaymentController::class, 'auditLogs'])->name('audit-logs');
        Route::get('/export', [SuperAdminPaymentController::class, 'exportReport'])->name('export');
        Route::get('/{payment}', [SuperAdminPaymentController::class, 'show'])->name('show');

        // User withdrawals
        Route::post('/user-withdrawals/{id}/approve', [SuperAdminPaymentController::class, 'approveUserWithdrawal'])->name('approve-user-withdrawal');
        Route::post('/user-withdrawals/{id}/reject', [SuperAdminPaymentController::class, 'rejectUserWithdrawal'])->name('reject-user-withdrawal');

        // Institution withdrawals
        Route::post('/institution-withdrawals/{id}/approve', [SuperAdminPaymentController::class, 'approveInstitutionWithdrawal'])->name('approve-institution-withdrawal');
        Route::post('/institution-withdrawals/{id}/complete', [SuperAdminPaymentController::class, 'completeInstitutionWithdrawal'])->name('complete-institution-withdrawal');
        Route::post('/institution-withdrawals/{id}/reject', [SuperAdminPaymentController::class, 'rejectInstitutionWithdrawal'])->name('reject-institution-withdrawal');

        // Author payouts
        Route::post('/author-payouts/{transactionId}/approve', [SuperAdminPaymentController::class, 'approveAuthorPayout'])->name('approve-author-payout');
        Route::post('/author-payouts/{transactionId}/reject', [SuperAdminPaymentController::class, 'rejectAuthorPayout'])->name('reject-author-payout');

        // Transaction management
        Route::delete('/transactions/{id}', [SuperAdminPaymentController::class, 'deleteTransaction'])->name('delete-transaction');
        Route::post('/transactions/bulk-delete', [SuperAdminPaymentController::class, 'bulkDeleteTransactions'])->name('bulk-delete-transactions');

        // Payment delete
        Route::delete('/{id}', [SuperAdminPaymentController::class, 'deletePayment'])->name('delete-payment');
    });

    // Withdrawal show
    Route::get('/withdrawals/{withdrawal}', [SuperAdminPaymentController::class, 'withdrawalShow'])->name('withdrawals.show');

    // Quotes
    Route::get('/quotes', [QuoteController::class, 'index'])->name('quotes.index');
    Route::get('/quotes/create', [QuoteController::class, 'create'])->name('quotes.create');
    Route::post('/quotes', [QuoteController::class, 'store'])->name('quotes.store');
    Route::get('/quotes/{quote}/edit', [QuoteController::class, 'edit'])->name('quotes.edit');
    Route::put('/quotes/{quote}', [QuoteController::class, 'update'])->name('quotes.update');
    Route::delete('/quotes/{quote}', [QuoteController::class, 'destroy'])->name('quotes.destroy');
    Route::get('/quotes/{quote}/analytics', [QuoteController::class, 'analytics'])->name('quotes.analytics');

    // Subscriptions
    Route::prefix('subscriptions')->name('subscriptions.')->group(function () {
        Route::get('/', [SuperAdminSubscriptionController::class, 'index'])->name('index');
        Route::get('/{id}', [SuperAdminSubscriptionController::class, 'show'])->name('show');
        Route::post('/{id}/override', [SuperAdminSubscriptionController::class, 'override'])->name('override');
        Route::post('/{id}/activate', [SuperAdminSubscriptionController::class, 'activate'])->name('activate');
        Route::post('/{id}/cancel', [SuperAdminSubscriptionController::class, 'cancel'])->name('cancel');
        Route::post('/{id}/expire', [SuperAdminSubscriptionController::class, 'markExpired'])->name('expire');
        Route::post('/bulk', [SuperAdminSubscriptionController::class, 'bulkAction'])->name('bulk');
        Route::get('/export', [SuperAdminSubscriptionController::class, 'export'])->name('export');
    });
});


// ==========================================
// SECTION 14: MEDIA TEAM ROUTES
// ==========================================

Route::middleware(['auth', MediaTeamMiddleware::class])->prefix('super-admin')->name('super-admin.')->group(function () {

    // Media Dashboard
    Route::get('/media-dashboard', function () {
        return view('super-admin.media-dashboard', [
            'heroSlidesCount' => \App\Models\HeroSlide::count(),
            'newsItemsCount' => \App\Models\NewsItem::count(),
            'foundersCount' => \App\Models\Founder::count(),
            'siteSettingsCount' => \App\Models\SiteSetting::count(),
        ]);
    })->name('media.dashboard');

    // Hero Slides
    Route::resource('hero-slides', App\Http\Controllers\SuperAdmin\HeroSlideController::class);
    Route::post('hero-slides/{heroSlide}/toggle-status', [App\Http\Controllers\SuperAdmin\HeroSlideController::class, 'toggleStatus'])->name('hero-slides.toggle-status');
    Route::post('hero-slides/reorder', [App\Http\Controllers\SuperAdmin\HeroSlideController::class, 'reorder'])->name('hero-slides.reorder');

    // News Items
    Route::resource('news-items', App\Http\Controllers\SuperAdmin\NewsItemController::class);
    Route::post('news-items/{newsItem}/toggle-featured', [App\Http\Controllers\SuperAdmin\NewsItemController::class, 'toggleFeatured'])->name('news-items.toggle-featured');
    Route::post('news-items/{newsItem}/toggle-status', [App\Http\Controllers\SuperAdmin\NewsItemController::class, 'toggleStatus'])->name('news-items.toggle-status');
    Route::post('news-items/reorder', [App\Http\Controllers\SuperAdmin\NewsItemController::class, 'reorder'])->name('news-items.reorder');

    // Founders
    Route::resource('founders', App\Http\Controllers\SuperAdmin\FounderController::class);
    Route::post('founders/{founder}/toggle-status', [App\Http\Controllers\SuperAdmin\FounderController::class, 'toggleStatus'])->name('founders.toggle-status');
    Route::post('founders/reorder', [App\Http\Controllers\SuperAdmin\FounderController::class, 'reorder'])->name('founders.reorder');

    // Site Settings
    Route::get('site-settings', [App\Http\Controllers\SuperAdmin\SiteSettingController::class, 'index'])->name('site-settings.index');
    Route::put('site-settings', [App\Http\Controllers\SuperAdmin\SiteSettingController::class, 'update'])->name('site-settings.update');
});


// ==========================================
// SECTION 15: COMMUNITY MANAGEMENT (Admin & Super Admin)
// ==========================================

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/communities', [App\Http\Controllers\Admin\CommunityController::class, 'index'])->name('communities.index');
    Route::get('/communities/{group}', [App\Http\Controllers\Admin\CommunityController::class, 'show'])->name('communities.show');
    Route::delete('/communities/{group}', [App\Http\Controllers\Admin\CommunityController::class, 'destroy'])->name('communities.destroy');
    Route::post('/communities/{group}/toggle-status', [App\Http\Controllers\Admin\CommunityController::class, 'toggleStatus'])->name('communities.toggle-status');
});

Route::middleware(['auth', 'superadmin'])->prefix('super-admin')->name('super-admin.')->group(function () {
    Route::get('/communities', [App\Http\Controllers\SuperAdmin\CommunityController::class, 'index'])->name('communities.index');
    Route::get('/communities/{group}', [App\Http\Controllers\SuperAdmin\CommunityController::class, 'show'])->name('communities.show');
    Route::delete('/communities/{group}', [App\Http\Controllers\SuperAdmin\CommunityController::class, 'destroy'])->name('communities.destroy');
    Route::post('/communities/{group}/toggle-status', [App\Http\Controllers\SuperAdmin\CommunityController::class, 'toggleStatus'])->name('communities.toggle-status');
    Route::post('/communities/{group}/feature', [App\Http\Controllers\SuperAdmin\CommunityController::class, 'toggleFeature'])->name('communities.toggle-feature');
});


// ==========================================
// SECTION 16: AUTHENTICATION ROUTES
// ==========================================

require __DIR__.'/auth.php';