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
use App\Http\Controllers\GlobalSearchController;
use App\Http\Controllers\InstitutionController;
use App\Http\Controllers\JoinRequestController;
use App\Http\Controllers\InstitutionQuoteController;

// ==========================================
// PUBLIC ROUTES (No login required)
// ==========================================
Route::get('/', [FrontController::class, 'welcome'])->name('welcome');
Route::get('/home', [FrontController::class, 'home'])->name('home');

// ==========================================
// REFERRAL PROCESS (Public)
// ==========================================
Route::get('/refer/{code}', [ReferralController::class, 'processReferral'])->name('referral.process');

// ==========================================
// PAYMENT WEBHOOKS & CALLBACKS (Public)
// ==========================================
Route::post('/webhooks/mpesa', [App\Http\Controllers\PaymentWebhookController::class, 'handleMpesaCallback']);
Route::post('/webhooks/stripe', [App\Http\Controllers\PaymentWebhookController::class, 'handleStripeWebhook']);
Route::post('/api/payments/mpesa/callback', [App\Http\Controllers\MultiPaymentController::class, 'mpesaCallback'])->name('payment.mpesa.callback');
Route::post('/api/payments/tigopesa/callback', [App\Http\Controllers\MultiPaymentController::class, 'tigopesaCallback'])->name('payment.tigopesa.callback');
Route::post('/api/payments/halopesa/callback', [App\Http\Controllers\MultiPaymentController::class, 'halopesaCallback'])->name('payment.halopesa.callback');
Route::post('/stripe/webhook', [App\Http\Controllers\MultiPaymentController::class, 'stripeWebhook'])->name('stripe.webhook');

// ==========================================
// PUBLIC PROFILE ROUTE
// ==========================================
Route::get('/@{username}', [ProfileController::class, 'show'])->name('profile.show');

// ==========================================
// AUTHENTICATED ROUTES (Login required)
// ==========================================
Route::middleware(['auth'])->group(function () {
    
    // ==========================================
    // DASHBOARD & CORE
    // ==========================================
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/leaderboard', [App\Http\Controllers\LeaderboardController::class, 'index'])->name('leaderboard.index');
    Route::get('/leaderboard/data', [App\Http\Controllers\LeaderboardController::class, 'index'])->name('leaderboard.data');
    
    // ==========================================
    // INSTITUTION DISCOVERY (All users)
    // ==========================================
    Route::get('/my-institution', [InstitutionController::class, 'myInstitution'])->name('my.institution');
    Route::get('/discover-institutions', [InstitutionController::class, 'discover'])->name('discover.institutions');
    Route::get('/institutions/{id}', [InstitutionController::class, 'show'])->name('institution.show');
    
    // Join Request Routes (All users)
    Route::post('/join-requests', [JoinRequestController::class, 'store'])->name('join-requests.store');
    Route::delete('/join-requests/{joinRequest}', [JoinRequestController::class, 'cancel'])->name('join-requests.cancel');
    Route::get('/join-requests/my-requests', [JoinRequestController::class, 'myRequests'])->name('join-requests.my-requests');
    
    // ==========================================
    // GLOBAL SEARCH
    // ==========================================
    Route::get('/api/global-search', [GlobalSearchController::class, 'api'])->name('global.search.api');
    Route::get('/global-search', [GlobalSearchController::class, 'index'])->name('global.search');
    
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
    // BOOK PURCHASE ROUTES
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
    
    // ==========================================
    // WALLET ROUTES
    // ==========================================
    Route::prefix('wallet')->name('wallet.')->group(function () {
        Route::get('/', [WalletController::class, 'index'])->name('index');
        Route::get('/balance', [WalletController::class, 'getBalance'])->name('balance');
        Route::post('/withdraw', [WalletController::class, 'withdraw'])->name('withdraw');
        Route::post('/topup', [WalletController::class, 'topUp'])->name('topup');
    });
    
    // ==========================================
    // REFERRAL ROUTES
    // ==========================================
    Route::prefix('referrals')->name('referrals.')->group(function () {
        Route::get('/', [ReferralController::class, 'index'])->name('index');
        Route::post('/{id}/complete', [ReferralController::class, 'markComplete'])->name('complete');
    });
    
    // ==========================================
    // SEARCH ROUTES
    // ==========================================
    Route::prefix('search')->name('search.')->group(function () {
        Route::get('/', [SearchController::class, 'index'])->name('index');
        Route::get('/live', [SearchController::class, 'live'])->name('live');
        Route::get('/filter', [SearchController::class, 'filter'])->name('filter');
    });
    
    // ==========================================
    // NOTIFICATION ROUTES
    // ==========================================
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::get('/unread-count', [NotificationController::class, 'getUnreadCount'])->name('unread-count');
        Route::get('/latest', [NotificationController::class, 'getLatest'])->name('latest');
        Route::post('/mark-all-read', [NotificationController::class, 'markAllRead'])->name('mark-all-read');
        Route::post('/{id}/read', [NotificationController::class, 'markAsRead'])->name('mark-read');
        Route::delete('/{id}', [NotificationController::class, 'destroy'])->name('destroy');
    });
    
    // ==========================================
    // BOOKMARK ROUTES
    // ==========================================
    Route::post('/bookmark/toggle', [BookmarkController::class, 'toggle'])->name('bookmark.toggle');
    Route::get('/bookmarks', [BookmarkController::class, 'index'])->name('bookmarks.index');
    Route::delete('/bookmarks/{id}', [BookmarkController::class, 'destroy'])->name('bookmark.destroy');
    
    // ==========================================
    // RATING & REVIEW ROUTES
    // ==========================================
    Route::post('/books/{book}/rate', [RatingReviewController::class, 'rate'])->name('books.rate');
    Route::post('/books/{book}/review', [RatingReviewController::class, 'review'])->name('books.review');
    Route::post('/reviews/{review}/helpful', [RatingReviewController::class, 'helpful'])->name('reviews.helpful');
    Route::delete('/books/{book}/rating', [RatingReviewController::class, 'deleteRating'])->name('books.rating.delete');
    Route::delete('/books/{book}/review', [RatingReviewController::class, 'deleteReview'])->name('books.review.delete');
    
    // ==========================================
    // QUIZ ROUTES
    // ==========================================
    Route::get('/quizzes', [QuizController::class, 'index'])->name('quizzes.index');
    Route::get('/quizzes/{id}', [QuizController::class, 'show'])->name('quizzes.show');
    Route::post('/quizzes/{id}/submit', [QuizController::class, 'submit'])->name('quizzes.submit');
    Route::get('/quizzes/results/{attemptId}', [QuizController::class, 'results'])->name('quizzes.results');
    Route::get('/quizzes/history', [QuizController::class, 'history'])->name('quizzes.history');
    
    // ==========================================
    // PROFILE ROUTES
    // ==========================================
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
        Route::put('/update', [ProfileController::class, 'update'])->name('update');
        Route::post('/avatar', [ProfileController::class, 'updateAvatar'])->name('avatar');
        Route::post('/cover', [ProfileController::class, 'updateCover'])->name('cover');
        Route::delete('/avatar', [ProfileController::class, 'deleteAvatar'])->name('avatar.delete');
        Route::delete('/cover', [ProfileController::class, 'deleteCover'])->name('cover.delete');
    });
    
    // ==========================================
    // AI ROUTES
    // ==========================================
    Route::get('/ai/chat', [AIController::class, 'index'])->name('ai.chat');
    Route::get('/ai/chat/{chat_session}', [AIController::class, 'index'])->name('ai.chat.session');
    Route::post('/ai/send', [AIController::class, 'sendMessage'])->name('ai.send');
    Route::post('/ai/new-session', [AIController::class, 'newSession'])->name('ai.new');
    Route::delete('/ai/session/{id}', [AIController::class, 'deleteSession'])->name('ai.delete');
    Route::get('/ai/session/{id}', [AIController::class, 'getSession'])->name('ai.get');
    
    // ==========================================
    // CERTIFICATE ROUTES
    // ==========================================
    Route::prefix('certificates')->name('certificates.')->group(function () {
        Route::get('/', [CertificateController::class, 'index'])->name('index');
        Route::get('/generate/{book}', [CertificateController::class, 'generate'])->name('generate');
        Route::get('/show/{certificate}', [CertificateController::class, 'show'])->name('show');
        Route::get('/download/{certificate}', [CertificateController::class, 'download'])->name('download');
    });
    
    // ==========================================
    // COMMUNITY SYSTEM
    // ==========================================
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
    
    // ==========================================
    // CONVERTER ROUTES
    // ==========================================
    Route::prefix('converter')->name('converter.')->group(function () {
        Route::get('/', [ConverterController::class, 'index'])->name('index');
        Route::post('/pdf-to-word', [ConverterController::class, 'pdfToWord'])->name('pdf-to-word');
        Route::post('/word-to-pdf', [ConverterController::class, 'wordToPdf'])->name('word-to-pdf');
        Route::post('/book-to-audio', [ConverterController::class, 'bookToAudio'])->name('book-to-audio');
    });
    
    // ==========================================
    // MARKETPLACE ROUTES
    // ==========================================
    Route::prefix('marketplace')->name('marketplace.')->group(function () {
        Route::get('/', [MarketplaceController::class, 'index'])->name('index');
        Route::get('/my-listings', [MarketplaceController::class, 'myListings'])->name('my-listings');
        Route::get('/create', [MarketplaceController::class, 'create'])->name('create');
        Route::post('/store', [MarketplaceController::class, 'store'])->name('store');
        Route::get('/{listing}', [MarketplaceController::class, 'show'])->name('show');
        Route::get('/{listing}/download', [MarketplaceController::class, 'download'])->name('download');
        Route::delete('/{listing}', [MarketplaceController::class, 'destroy'])->name('destroy');
    });
    
    // ==========================================
    // DOCUMENT ROUTES
    // ==========================================
    Route::prefix('documents')->name('documents.')->group(function () {
        Route::get('/', [DocumentController::class, 'index'])->name('index');
        Route::get('/create', [DocumentController::class, 'create'])->name('create');
        Route::post('/upload', [DocumentController::class, 'upload'])->name('upload');
        Route::get('/{document}', [DocumentController::class, 'show'])->name('show');
        Route::post('/{document}/ask', [DocumentController::class, 'ask'])->name('ask');
        Route::delete('/{document}', [DocumentController::class, 'destroy'])->name('destroy');
    });
    
    // ==========================================
    // APPLICATION ROUTES (for users to become author/bookseller)
    // ==========================================
    Route::get('/apply/{type}', [App\Http\Controllers\ApplicationController::class, 'create'])->name('applications.create');
    Route::post('/applications', [App\Http\Controllers\ApplicationController::class, 'store'])->name('applications.store');
    
    // ==========================================
    // INSTITUTION MEMBERS DIRECTORY
    // ==========================================
    Route::get('/institution/members/directory', [App\Http\Controllers\Institution\MemberController::class, 'directory'])->name('institution.members.directory');
    
    // ==========================================
    // AUTHOR ROUTES
    // ==========================================
    Route::prefix('author')->name('author.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Author\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/royalties', [App\Http\Controllers\Author\RoyaltyController::class, 'index'])->name('royalties.index');
        Route::resource('books', App\Http\Controllers\Author\BookController::class);
    });
    
    // ==========================================
    // USER WITHDRAWALS
    // ==========================================
    Route::prefix('withdrawals')->name('withdrawals.')->group(function () {
        Route::get('/', [App\Http\Controllers\WithdrawalController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\WithdrawalController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\WithdrawalController::class, 'store'])->name('store');
        Route::get('/{withdrawal}', [App\Http\Controllers\WithdrawalController::class, 'show'])->name('show');
        Route::post('/{withdrawal}/cancel', [App\Http\Controllers\WithdrawalController::class, 'cancel'])->name('cancel');
    });
    
    // ==========================================
    // INSTRUCTOR COURSE ROUTES
    // ==========================================
    Route::prefix('instructor')->name('instructor.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Instructor\DashboardController::class, 'index'])->name('dashboard');
        Route::resource('courses', App\Http\Controllers\Instructor\CourseController::class);
        Route::post('/courses/{course}/lessons', [App\Http\Controllers\Instructor\CourseController::class, 'addLesson'])->name('courses.lessons.store');
        Route::put('/lessons/{lesson}', [App\Http\Controllers\Instructor\CourseController::class, 'updateLesson'])->name('lessons.update');
        Route::delete('/lessons/{lesson}', [App\Http\Controllers\Instructor\CourseController::class, 'deleteLesson'])->name('lessons.destroy');
        Route::get('/courses/{course}/enrollments', [App\Http\Controllers\Instructor\CourseController::class, 'enrollments'])->name('courses.enrollments');
    });
    
    // ==========================================
    // INSTITUTION ADMIN ROUTES (ALL in ONE group)
    // ==========================================
    Route::middleware(['auth', 'institution'])->prefix('institution')->name('institution.')->group(function () {
        // Dashboard
        Route::get('/dashboard', [App\Http\Controllers\Institution\DashboardController::class, 'index'])->name('dashboard');
        
        // Books
        Route::get('/books', [App\Http\Controllers\Institution\BookController::class, 'index'])->name('books.index');
        
        // Members
        Route::resource('members', App\Http\Controllers\Institution\MemberController::class);
        Route::post('/members/{member}/role', [App\Http\Controllers\Institution\MemberController::class, 'updateRole'])->name('members.update-role');
        
        // Withdrawals
        Route::resource('withdrawals', App\Http\Controllers\Institution\WithdrawalController::class);
        Route::post('/withdrawals/{withdrawal}/cancel', [App\Http\Controllers\Institution\WithdrawalController::class, 'cancel'])->name('withdrawals.cancel');
        
        // Quotes - Using InstitutionQuoteController
        Route::get('/quotes', [App\Http\Controllers\InstitutionQuoteController::class, 'index'])->name('quotes.index');
        Route::get('/quotes/create', [App\Http\Controllers\InstitutionQuoteController::class, 'create'])->name('quotes.create');
        Route::post('/quotes', [App\Http\Controllers\InstitutionQuoteController::class, 'store'])->name('quotes.store');
        Route::get('/quotes/{quote}/edit', [App\Http\Controllers\InstitutionQuoteController::class, 'edit'])->name('quotes.edit');
        Route::put('/quotes/{quote}', [App\Http\Controllers\InstitutionQuoteController::class, 'update'])->name('quotes.update');
        Route::delete('/quotes/{quote}', [App\Http\Controllers\InstitutionQuoteController::class, 'destroy'])->name('quotes.destroy');
        Route::get('/quotes/{quote}/analytics', [App\Http\Controllers\InstitutionQuoteController::class, 'analytics'])->name('quotes.analytics');
    });
});

// ==========================================
// SUPER ADMIN ROUTES
// ==========================================
Route::middleware(['auth', 'superadmin'])->prefix('super-admin')->name('super-admin.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\SuperAdmin\DashboardController::class, 'index'])->name('dashboard');
    
    Route::resource('books', App\Http\Controllers\SuperAdmin\BookController::class);
    Route::post('/books/{book}/toggle-status', [App\Http\Controllers\SuperAdmin\BookController::class, 'toggleStatus'])->name('books.toggle-status');
    Route::post('/books/bulk-action', [App\Http\Controllers\SuperAdmin\BookController::class, 'bulkAction'])->name('books.bulk-action');
    
    Route::resource('users', App\Http\Controllers\SuperAdmin\UserController::class);
    
    Route::resource('institutions', App\Http\Controllers\SuperAdmin\InstitutionController::class);
    Route::post('/institutions/{institution}/approve', [App\Http\Controllers\SuperAdmin\InstitutionController::class, 'approve'])->name('institutions.approve');
    Route::post('/institutions/{institution}/suspend', [App\Http\Controllers\SuperAdmin\InstitutionController::class, 'suspend'])->name('institutions.suspend');
    Route::post('/institutions/{institution}/reject', [App\Http\Controllers\SuperAdmin\InstitutionController::class, 'reject'])->name('institutions.reject');
    
    Route::prefix('marketplace')->name('marketplace.')->group(function () {
        Route::get('/', [App\Http\Controllers\SuperAdmin\MarketplaceController::class, 'index'])->name('index');
        Route::get('/{listing}', [App\Http\Controllers\SuperAdmin\MarketplaceController::class, 'show'])->name('show');
        Route::post('/{listing}/approve', [App\Http\Controllers\SuperAdmin\MarketplaceController::class, 'approve'])->name('approve');
        Route::post('/{listing}/reject', [App\Http\Controllers\SuperAdmin\MarketplaceController::class, 'reject'])->name('reject');
        Route::delete('/{listing}', [App\Http\Controllers\SuperAdmin\MarketplaceController::class, 'destroy'])->name('destroy');
    });
    
    Route::prefix('applications')->name('applications.')->group(function () {
        Route::get('/', [App\Http\Controllers\SuperAdmin\ApplicationController::class, 'index'])->name('index');
        Route::get('/{application}', [App\Http\Controllers\SuperAdmin\ApplicationController::class, 'show'])->name('show');
        Route::post('/{application}/approve', [App\Http\Controllers\SuperAdmin\ApplicationController::class, 'approve'])->name('approve');
        Route::post('/{application}/reject', [App\Http\Controllers\SuperAdmin\ApplicationController::class, 'reject'])->name('reject');
        Route::get('/{application}/download/{document}', [App\Http\Controllers\SuperAdmin\ApplicationController::class, 'download'])->name('download');
        Route::delete('/{application}', [App\Http\Controllers\SuperAdmin\ApplicationController::class, 'destroy'])->name('destroy');
    });
    
    Route::prefix('analytics')->name('analytics.')->group(function () {
        Route::get('/', [App\Http\Controllers\SuperAdmin\AnalyticsController::class, 'index'])->name('index');
        Route::get('/data', [App\Http\Controllers\SuperAdmin\AnalyticsController::class, 'getData'])->name('data');
        Route::get('/export', [App\Http\Controllers\SuperAdmin\AnalyticsController::class, 'export'])->name('export');
    });
    
    Route::prefix('payments')->name('payments.')->group(function () {
        Route::get('/', [App\Http\Controllers\SuperAdmin\PaymentController::class, 'index'])->name('index');
        Route::get('/transactions', [App\Http\Controllers\SuperAdmin\PaymentController::class, 'transactions'])->name('transactions');
        Route::get('/withdrawals', [App\Http\Controllers\SuperAdmin\PaymentController::class, 'withdrawals'])->name('withdrawals');
        Route::get('/{payment}', [App\Http\Controllers\SuperAdmin\PaymentController::class, 'show'])->name('show');
    });
    
    Route::get('/withdrawals/{withdrawal}', [App\Http\Controllers\SuperAdmin\PaymentController::class, 'withdrawalShow'])->name('withdrawals.show');
    
    // Quote Management - Global quotes
    Route::get('/quotes', [App\Http\Controllers\QuoteController::class, 'index'])->name('quotes.index');
    Route::get('/quotes/create', [App\Http\Controllers\QuoteController::class, 'create'])->name('quotes.create');
    Route::post('/quotes', [App\Http\Controllers\QuoteController::class, 'store'])->name('quotes.store');
    Route::get('/quotes/{quote}/edit', [App\Http\Controllers\QuoteController::class, 'edit'])->name('quotes.edit');
    Route::put('/quotes/{quote}', [App\Http\Controllers\QuoteController::class, 'update'])->name('quotes.update');
    Route::delete('/quotes/{quote}', [App\Http\Controllers\QuoteController::class, 'destroy'])->name('quotes.destroy');
    Route::get('/quotes/{quote}/analytics', [App\Http\Controllers\QuoteController::class, 'analytics'])->name('quotes.analytics');
});

// ==========================================
// ADMIN ROUTES
// ==========================================
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics');
    Route::get('/analytics/data', [AnalyticsController::class, 'getData'])->name('analytics.data');
    
    Route::resource('books', AdminBookController::class);
    Route::post('/books/{book}/toggle-status', [AdminBookController::class, 'toggleStatus'])->name('books.toggle-status');
    Route::post('/books/bulk-action', [AdminBookController::class, 'bulkAction'])->name('books.bulk-action');
    
    Route::resource('users', AdminUserController::class);
    Route::post('/users/{user}/toggle-role', [AdminUserController::class, 'toggleRole'])->name('users.toggle-role');
    
    Route::resource('institutions', App\Http\Controllers\Admin\InstitutionController::class);
    Route::post('/institutions/{institution}/approve', [App\Http\Controllers\Admin\InstitutionController::class, 'approve'])->name('institutions.approve');
    Route::post('/institutions/{institution}/reject', [App\Http\Controllers\Admin\InstitutionController::class, 'reject'])->name('institutions.reject');
    Route::post('/institutions/{institution}/suspend', [App\Http\Controllers\Admin\InstitutionController::class, 'suspend'])->name('institutions.suspend');
    
    Route::get('/institutions/{institution}/members', [App\Http\Controllers\Admin\InstitutionMemberController::class, 'index'])->name('institutions.members');
    Route::get('/institutions/{institution}/members/create', [App\Http\Controllers\Admin\InstitutionMemberController::class, 'create'])->name('institutions.members.create');
    Route::post('/institutions/{institution}/members', [App\Http\Controllers\Admin\InstitutionMemberController::class, 'store'])->name('institutions.members.store');
    Route::delete('/institutions/{institution}/members/{member}', [App\Http\Controllers\Admin\InstitutionMemberController::class, 'destroy'])->name('institutions.members.destroy');
    
    Route::get('/marketplace/pending', [AdminMarketplaceController::class, 'pending'])->name('marketplace.pending');
    Route::get('/marketplace/all', [AdminMarketplaceController::class, 'all'])->name('marketplace.all');
    Route::post('/marketplace/{listing}/approve', [AdminMarketplaceController::class, 'approve'])->name('marketplace.approve');
    Route::post('/marketplace/{listing}/reject', [AdminMarketplaceController::class, 'reject'])->name('marketplace.reject');
    
    Route::get('/applications', [App\Http\Controllers\ApplicationController::class, 'index'])->name('applications.index');
    Route::get('/applications/{application}', [App\Http\Controllers\ApplicationController::class, 'show'])->name('applications.show');
    Route::post('/applications/{application}/approve', [App\Http\Controllers\ApplicationController::class, 'approve'])->name('applications.approve');
    Route::post('/applications/{application}/reject', [App\Http\Controllers\ApplicationController::class, 'reject'])->name('applications.reject');
    Route::get('/applications/{application}/download/{document}', [App\Http\Controllers\ApplicationController::class, 'download'])->name('applications.download');
    
    Route::prefix('payments')->name('payments.')->group(function () {
        Route::get('/', [AdminPaymentController::class, 'index'])->name('index');
        Route::get('/{id}', [AdminPaymentController::class, 'show'])->name('show');
        Route::post('/approve-withdrawal/{id}', [AdminPaymentController::class, 'approveWithdrawal'])->name('approve-withdrawal');
        Route::post('/reject-withdrawal/{id}', [AdminPaymentController::class, 'rejectWithdrawal'])->name('reject-withdrawal');
        Route::post('/approve-deposit/{id}', [AdminPaymentController::class, 'approveDeposit'])->name('approve-deposit');
        Route::post('/institution-withdrawals/{id}/approve', [AdminPaymentController::class, 'approveInstitutionWithdrawal'])->name('approve-institution-withdrawal');
        Route::post('/institution-withdrawals/{id}/complete', [AdminPaymentController::class, 'completeInstitutionWithdrawal'])->name('complete-institution-withdrawal');
        Route::post('/institution-withdrawals/{id}/reject', [AdminPaymentController::class, 'rejectInstitutionWithdrawal'])->name('reject-institution-withdrawal');
    });
    
    Route::get('/payments', [AdminPaymentController::class, 'index'])->name('payments.index');
    
    // Quote Management - Global quotes
    Route::get('/quotes', [App\Http\Controllers\QuoteController::class, 'index'])->name('quotes.index');
    Route::get('/quotes/create', [App\Http\Controllers\QuoteController::class, 'create'])->name('quotes.create');
    Route::post('/quotes', [App\Http\Controllers\QuoteController::class, 'store'])->name('quotes.store');
    Route::get('/quotes/{quote}/edit', [App\Http\Controllers\QuoteController::class, 'edit'])->name('quotes.edit');
    Route::put('/quotes/{quote}', [App\Http\Controllers\QuoteController::class, 'update'])->name('quotes.update');
    Route::delete('/quotes/{quote}', [App\Http\Controllers\QuoteController::class, 'destroy'])->name('quotes.destroy');
    Route::get('/quotes/{quote}/analytics', [App\Http\Controllers\QuoteController::class, 'analytics'])->name('quotes.analytics');
});

// ==========================================
// PUBLIC QUOTE ROUTES (for dashboard)
// ==========================================
Route::middleware(['auth'])->group(function () {
    Route::get('/api/quote-of-the-day', [App\Http\Controllers\QuoteController::class, 'quoteOfTheDay']);
    Route::post('/api/quote/{quote}/favorite', [App\Http\Controllers\QuoteController::class, 'toggleFavorite']);
    Route::post('/api/quote/{quote}/share', [App\Http\Controllers\QuoteController::class, 'share']);
    Route::get('/api/quote/next', [App\Http\Controllers\QuoteController::class, 'nextQuote']);
});

// ==========================================
// ADMIN BANK TRANSFER APPROVAL ROUTE
// ==========================================
Route::middleware(['auth', 'admin'])->post('/admin/payments/{paymentId}/approve', [App\Http\Controllers\PaymentWebhookController::class, 'approveBankTransfer'])->name('admin.payments.approve');

// ==========================================
// AUTHENTICATION ROUTES
// ==========================================
require __DIR__.'/auth.php';