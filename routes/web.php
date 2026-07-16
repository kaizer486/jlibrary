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
use App\Http\Controllers\AuthorWalletController;
use App\Http\Controllers\InstitutionCreationController;
use App\Http\Controllers\SuperAdmin\InstitutionRequestController;
use App\Http\Controllers\BookPurchaseController;
use App\Http\Controllers\UserLibraryController;
use App\Http\Controllers\Library\PublicController;
use App\Http\Controllers\Institution\LibraryController as InstitutionLibraryController;
use App\Http\Controllers\SellerController;
use App\Http\Controllers\Admin\ApplicationController as AdminApplicationController;
use App\Http\Controllers\SuperAdmin\ApplicationController as SuperAdminApplicationController;
use App\Http\Controllers\SuperAdmin\SubscriptionController as SuperAdminSubscriptionController;
use App\Http\Controllers\SuperAdmin\InstitutionSubscriptionController as SuperAdminInstitutionSubscriptionController;
use App\Http\Controllers\Institution\SubscriptionController as InstitutionSubscriptionController;
use App\Http\Controllers\BorrowRequestController;
use Illuminate\Support\Facades\File;


Route::get('/media/{path}', function ($path) {
    $fullPath = storage_path('app/public/' . $path);

    if (!file_exists($fullPath)) {
        abort(404);
    }

    return response()->file($fullPath, [
        'Content-Type' => File::mimeType($fullPath),
        'Cache-Control' => 'public, max-age=31536000',
    ]);
})->where('path', '.*');

// ==========================================
// SECTION 1: PUBLIC ROUTES (No login required)
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

if (app()->environment('local')) {
    Route::post('/test-webhook/mpesa', function () {
        return response()->json(['message' => 'Test webhook received']);
    });
}





Route::get('/storage/{path}', function ($path) {
    $fullPath = storage_path('app/public/' . $path);

    if (!file_exists($fullPath)) {
        abort(404);
    }

    $mime = mime_content_type($fullPath);

    return response()->file($fullPath, [
        'Content-Type' => $mime,
    ]);
})->where('path', '.*')->name('storage.local');





// ==========================================
// PUBLIC BORROW REQUEST ROUTES
// ==========================================
Route::middleware(['auth'])->group(function () {
    Route::get('/borrow/request/{book_id}/{institution_id}', [BorrowRequestController::class, 'create'])
        ->name('borrow.request.form');
    Route::post('/borrow/request', [BorrowRequestController::class, 'store'])
        ->name('borrow.request.store');
});



// ==========================================
// LIBRARIAN BORROW REQUEST MANAGEMENT
// ==========================================
Route::middleware(['auth', 'librarian'])->prefix('librarian')->group(function () {
    Route::get('/borrow-requests', [BorrowRequestController::class, 'index'])
        ->name('librarian.borrow-requests.index');
    Route::post('/borrow-requests/{id}/approve', [BorrowRequestController::class, 'approve'])
        ->name('librarian.borrow-requests.approve');
    Route::post('/borrow-requests/{id}/reject', [BorrowRequestController::class, 'reject'])
        ->name('librarian.borrow-requests.reject');
});

Route::get('/certificates/serve/{id}', [CertificateController::class, 'serve'])->name('certificates.serve');
Route::get('/@{username}', [ProfileController::class, 'show'])->name('profile.show');

// ==========================================
// INSTITUTION PUBLIC ROUTES
// ==========================================
Route::prefix('institution')->name('institution.public.')->group(function () {
    Route::get('/{institutionId}/library', [App\Http\Controllers\Library\PublicController::class, 'index'])->name('index');
    Route::get('/{institutionId}/library/{book}', [App\Http\Controllers\Library\PublicController::class, 'show'])->name('show');
    Route::get('/{institutionId}/shelf/{shelfId}', [App\Http\Controllers\Library\PublicController::class, 'shelfShow'])->name('shelf.show');
});

// ==========================================
// GLOBAL LIBRARY ROUTES
// ==========================================
Route::get('/library', [LibraryController::class, 'index'])->name('library.index');
// Individual book view - redirects to institution page
Route::get('/library/{id}', [LibraryController::class, 'show'])->name('library.show');

Route::get('/refer/{code}', [ReferralController::class, 'processReferral'])->name('referral.process');

// ==========================================
// PAYMENT WEBHOOKS
// ==========================================
Route::post('/webhooks/mpesa', [App\Http\Controllers\PaymentWebhookController::class, 'handleMpesaCallback']);
Route::post('/webhooks/tigopesa', [App\Http\Controllers\PaymentWebhookController::class, 'handleTigopesaCallback']);
Route::post('/webhooks/halopesa', [App\Http\Controllers\PaymentWebhookController::class, 'handleHalopesaCallback']);
Route::post('/webhooks/stripe', [App\Http\Controllers\PaymentWebhookController::class, 'handleStripeWebhook']);
Route::post('/webhooks/pesapal', [App\Http\Controllers\PaymentWebhookController::class, 'handlePesapalCallback']);
Route::get('/payment/pesapal/callback', [App\Http\Controllers\MultiPaymentController::class, 'pesapalCallback'])->name('payment.pesapal.callback');

// ==========================================
// SECTION 2: AUTHENTICATED ROUTES
// ==========================================
Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/leaderboard', [App\Http\Controllers\LeaderboardController::class, 'index'])->name('leaderboard.index');
    Route::get('/leaderboard/data', [App\Http\Controllers\LeaderboardController::class, 'index'])->name('leaderboard.data');

    Route::prefix('invoices')->name('invoices.')->group(function () {
        Route::get('/payment/{paymentId}', [App\Http\Controllers\InvoiceController::class, 'paymentInvoice'])->name('payment');
        Route::get('/transaction/{transactionId}', [App\Http\Controllers\InvoiceController::class, 'transactionInvoice'])->name('transaction');
        Route::get('/subscription/{subscriptionPaymentId}', [App\Http\Controllers\InvoiceController::class, 'subscriptionInvoice'])->name('subscription');
    });

    Route::get('/my-institution', [InstitutionController::class, 'myInstitution'])->name('my.institution');
    Route::get('/discover-institutions', [InstitutionController::class, 'discover'])->name('discover.institutions');
    Route::get('/institutions/{id}', [InstitutionController::class, 'show'])->name('institutions.show');
    
    Route::post('/institution/leave/{institutionId?}', [App\Http\Controllers\InstitutionController::class, 'leave'])
        ->name('institution.leave')->middleware('auth');

    Route::post('/institution/join/free/{id}', [App\Http\Controllers\InstitutionController::class, 'freeJoin'])
        ->name('institution.join.free');

    Route::get('/institution/create-request', [App\Http\Controllers\InstitutionCreationController::class, 'create'])
        ->name('institution.create-request');
    Route::post('/institution/create-request', [App\Http\Controllers\InstitutionCreationController::class, 'store'])
        ->name('institution.store-request');
    Route::get('/institution/my-requests', [App\Http\Controllers\InstitutionCreationController::class, 'myRequests'])
        ->name('institution.my-requests');
    Route::get('/institution/request/{id}', [App\Http\Controllers\InstitutionCreationController::class, 'show'])
        ->name('institution.request.show');
    Route::post('/institution/request/{id}/cancel', [App\Http\Controllers\InstitutionCreationController::class, 'cancel'])
        ->name('institution.request.cancel');

    Route::get('/join-requests', [App\Http\Controllers\JoinRequestController::class, 'myRequests'])
        ->name('join-requests.index');
    Route::post('/join-requests', [App\Http\Controllers\JoinRequestController::class, 'store'])
        ->name('join-requests.store');
    Route::delete('/join-requests/{joinRequest}', [App\Http\Controllers\JoinRequestController::class, 'cancel'])
        ->name('join-requests.cancel');

    Route::get('/api/global-search', [GlobalSearchController::class, 'api'])->name('global.search.api');
    Route::get('/global-search', [GlobalSearchController::class, 'index'])->name('global.search');

    Route::prefix('wallet')->name('wallet.')->group(function () {
        Route::get('/', [WalletController::class, 'index'])->name('index');
        Route::get('/balance', [WalletController::class, 'getBalance'])->name('balance');
        Route::post('/withdraw', [WalletController::class, 'withdraw'])->name('withdraw');
        Route::post('/topup', [WalletController::class, 'topUp'])->name('topup');
        Route::get('/export', [WalletController::class, 'exportTransactions'])->name('export');
    });

    Route::prefix('author')->name('author.')->group(function () {
        Route::get('/wallet', [AuthorWalletController::class, 'index'])->name('wallet');
        Route::post('/wallet/withdraw', [AuthorWalletController::class, 'requestWithdrawal'])->name('wallet.withdraw');
    });

    Route::prefix('user')->name('user.')->group(function () {
        Route::get('/subscription', [App\Http\Controllers\User\SubscriptionController::class, 'index'])->name('subscription.index');
        Route::get('/subscription/history', [App\Http\Controllers\User\SubscriptionController::class, 'history'])->name('subscription.history');
        Route::post('/subscription/extend', [App\Http\Controllers\User\SubscriptionController::class, 'extend'])->name('subscription.extend');
        Route::post('/subscription/cancel', [App\Http\Controllers\User\SubscriptionController::class, 'cancel'])->name('subscription.cancel');
    });

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


    Route::prefix('user/library')->name('user.library.')->group(function () {
        Route::get('/', [UserLibraryController::class, 'index'])->name('index');
        Route::get('/{bookId}', [UserLibraryController::class, 'show'])->name('show');
        Route::post('/{bookId}/progress', [UserLibraryController::class, 'updateProgress'])->name('progress');
        Route::post('/{bookId}/track', [UserLibraryController::class, 'trackView'])->name('track');
    });

    Route::get('/my-borrowings', [App\Http\Controllers\Librarian\BorrowingController::class, 'myBorrowings'])->name('my.borrowings');

    Route::prefix('payment')->name('payment.')->group(function () {
        Route::get('/methods', [App\Http\Controllers\MultiPaymentController::class, 'showMethods'])->name('methods');
        Route::post('/initiate', [App\Http\Controllers\MultiPaymentController::class, 'initiatePayment'])->name('initiate');
        Route::post('/save-details', [App\Http\Controllers\MultiPaymentController::class, 'savePaymentDetails'])->name('save-details');
        Route::get('/status/{paymentId}', [App\Http\Controllers\MultiPaymentController::class, 'checkStatus'])->name('status');
    });

    Route::prefix('referrals')->name('referrals.')->group(function () {
        Route::get('/', [ReferralController::class, 'index'])->name('index');
        Route::post('/{id}/complete', [ReferralController::class, 'markComplete'])->name('complete');
    });

    Route::prefix('search')->name('search.')->group(function () {
        Route::get('/', [SearchController::class, 'index'])->name('index');
        Route::get('/live', [SearchController::class, 'live'])->name('live');
        Route::get('/filter', [SearchController::class, 'filter'])->name('filter');
    });

    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::get('/unread-count', [NotificationController::class, 'getUnreadCount'])->name('unread-count');
        Route::get('/latest', [NotificationController::class, 'getLatest'])->name('latest');
        Route::post('/mark-all-read', [NotificationController::class, 'markAllRead'])->name('mark-all-read');
        Route::post('/{id}/read', [NotificationController::class, 'markAsRead'])->name('mark-read');
        Route::delete('/{id}', [NotificationController::class, 'destroy'])->name('destroy');
    });

    Route::post('/bookmark/toggle', [BookmarkController::class, 'toggle'])->name('bookmark.toggle');
    Route::get('/bookmarks', [BookmarkController::class, 'index'])->name('bookmarks.index');
    Route::delete('/bookmarks/{id}', [BookmarkController::class, 'destroy'])->name('bookmark.destroy');

    Route::post('/books/{book}/rate', [RatingReviewController::class, 'rate'])->name('books.rate');
    Route::post('/books/{book}/review', [RatingReviewController::class, 'review'])->name('books.review');
    Route::post('/reviews/{review}/helpful', [RatingReviewController::class, 'helpful'])->name('reviews.helpful');
    Route::delete('/books/{book}/rating', [RatingReviewController::class, 'deleteRating'])->name('books.rating.delete');
    Route::delete('/books/{book}/review', [RatingReviewController::class, 'deleteReview'])->name('books.review.delete');

    Route::get('/quizzes', [QuizController::class, 'index'])->name('quizzes.index');
    Route::get('/quizzes/{id}', [QuizController::class, 'show'])->name('quizzes.show');
    Route::post('/quizzes/{id}/submit', [QuizController::class, 'submit'])->name('quizzes.submit');
    Route::get('/quizzes/results/{attemptId}', [QuizController::class, 'results'])->name('quizzes.results');
    Route::get('/quizzes/history', [QuizController::class, 'history'])->name('quizzes.history');

    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
        Route::put('/update', [ProfileController::class, 'update'])->name('update');
        Route::post('/avatar', [ProfileController::class, 'updateAvatar'])->name('avatar');
        Route::post('/cover', [ProfileController::class, 'updateCover'])->name('cover');
        Route::delete('/avatar', [ProfileController::class, 'deleteAvatar'])->name('avatar.delete');
        Route::delete('/cover', [ProfileController::class, 'deleteCover'])->name('cover.delete');
    });

    Route::get('/ai/chat', [AIController::class, 'index'])->name('ai.chat');
    Route::get('/ai/chat/{chat_session}', [AIController::class, 'index'])->name('ai.chat.session');
    Route::post('/ai/send', [AIController::class, 'sendMessage'])->name('ai.send');
    Route::post('/ai/new-session', [AIController::class, 'newSession'])->name('ai.new');
    Route::delete('/ai/session/{id}', [AIController::class, 'deleteSession'])->name('ai.delete');
    Route::get('/ai/session/{id}', [AIController::class, 'getSession'])->name('ai.get');

    Route::prefix('certificates')->name('certificates.')->group(function () {
        Route::get('/', [CertificateController::class, 'index'])->name('index');
        Route::get('/generate/{book}', [CertificateController::class, 'generate'])->name('generate');
        Route::get('/show/{certificate}', [CertificateController::class, 'show'])->name('show');
        Route::get('/download/{certificate}', [CertificateController::class, 'download'])->name('download');
        Route::get('/fix-missing', [CertificateController::class, 'fixMissingCertificates'])->name('fix-missing');
    });

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

    Route::prefix('converter')->name('converter.')->group(function () {
        Route::get('/', [ConverterController::class, 'index'])->name('index');
        Route::post('/pdf-to-word', [ConverterController::class, 'pdfToWord'])->name('pdf-to-word');
        Route::post('/word-to-pdf', [ConverterController::class, 'wordToPdf'])->name('word-to-pdf');
        Route::post('/book-to-audio', [ConverterController::class, 'bookToAudio'])->name('book-to-audio');
    });

    Route::prefix('documents')->name('documents.')->group(function () {
        Route::get('/', [DocumentController::class, 'index'])->name('index');
        Route::get('/create', [DocumentController::class, 'create'])->name('create');
        Route::post('/upload', [DocumentController::class, 'upload'])->name('upload');
        Route::get('/{document}', [DocumentController::class, 'show'])->name('show');
        Route::post('/{document}/ask', [DocumentController::class, 'ask'])->name('ask');
        Route::delete('/{document}', [DocumentController::class, 'destroy'])->name('destroy');
    });

    Route::get('/apply/{type}', [App\Http\Controllers\ApplicationController::class, 'create'])->name('applications.create');
    Route::post('/applications', [App\Http\Controllers\ApplicationController::class, 'store'])->name('applications.store');

    Route::get('/institution/members/directory', [App\Http\Controllers\Institution\MemberController::class, 'directory'])->name('institution.members.directory');

    Route::prefix('withdrawals')->name('withdrawals.')->group(function () {
        Route::get('/', [App\Http\Controllers\WithdrawalController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\WithdrawalController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\WithdrawalController::class, 'store'])->name('store');
        Route::get('/{withdrawal}', [App\Http\Controllers\WithdrawalController::class, 'show'])->name('show');
        Route::post('/{withdrawal}/cancel', [App\Http\Controllers\WithdrawalController::class, 'cancel'])->name('cancel');
    });

    Route::get('/api/quote-of-the-day', [App\Http\Controllers\QuoteController::class, 'quoteOfTheDay']);
    Route::post('/api/quote/{quote}/favorite', [App\Http\Controllers\QuoteController::class, 'toggleFavorite']);
    Route::post('/api/quote/{quote}/share', [App\Http\Controllers\QuoteController::class, 'share']);
    Route::get('/api/quote/next', [App\Http\Controllers\QuoteController::class, 'nextQuote']);
});

// ==========================================
// SECTION 3: INSTITUTION ROUTES
// ==========================================

Route::prefix('institution')->middleware(['auth'])->group(function () {
    Route::post('/books/{book}/progress', [App\Http\Controllers\Institution\BookController::class, 'updateProgress'])
        ->name('institution.books.progress');
});
// ==========================================
// INSTITUTION LIBRARY ROUTES (Admin Only)
// ==========================================
Route::middleware(['auth', 'institution'])->prefix('institution')->name('institution.')->group(function () {
    Route::get('/books', [App\Http\Controllers\Institution\LibraryController::class, 'index'])->name('books.index');
    Route::get('/books/{book}', [App\Http\Controllers\Institution\LibraryController::class, 'show'])->name('books.show');
    Route::get('/books/shelf/{shelfId}/books', [App\Http\Controllers\Institution\LibraryController::class, 'getShelfBooks'])->name('books.shelf.books');
    Route::get('/books/stats', [App\Http\Controllers\Institution\LibraryController::class, 'getStats'])->name('books.stats');
});

// ==========================================
// GLOBAL LIBRARY ROUTES (Public)
// ==========================================
Route::get('/library', [LibraryController::class, 'index'])->name('library.index');
Route::get('/library/{id}', [LibraryController::class, 'show'])->name('library.show');
Route::middleware(['auth'])->group(function () {
    Route::get('/orders/create/{book}', [App\Http\Controllers\Institution\OrderController::class, 'create'])
        ->name('orders.create');
    Route::post('/orders', [App\Http\Controllers\Institution\OrderController::class, 'store'])
        ->name('orders.store');
    Route::get('/orders/{order}', [App\Http\Controllers\Institution\OrderController::class, 'show'])
        ->name('orders.show');
    
    Route::prefix('institution')->name('institution.')->middleware(['auth', 'institution'])->group(function () {
        Route::get('/orders', [App\Http\Controllers\Institution\OrderController::class, 'index'])
            ->name('orders.index');
        Route::post('/orders/{order}/status', [App\Http\Controllers\Institution\OrderController::class, 'updateStatus'])
            ->name('orders.update-status');
    });
});

Route::middleware(['auth', 'institution'])
    ->prefix('institution')
    ->name('institution.subscription.')
    ->group(function () {
        Route::post('/initiate-payment', [InstitutionSubscriptionController::class, 'initiatePayment'])
            ->name('initiate-payment');
        Route::get('/payment-status/{subscriptionId}', [InstitutionSubscriptionController::class, 'paymentStatus'])
            ->name('payment-status');
        Route::get('/payment-instructions/{subscriptionId}', [InstitutionSubscriptionController::class, 'paymentInstructions'])
            ->name('payment-instructions');
    });

// ==========================================
// SECTION 4: INSTITUTION ADMIN ROUTES
// ==========================================
Route::middleware(['auth', 'institution'])
    ->prefix('institution')
    ->name('institution.')
    ->group(function () {
        
        Route::get('/dashboard', [App\Http\Controllers\Institution\DashboardController::class, 'index'])->name('dashboard');
        
        Route::resource('books', App\Http\Controllers\Institution\BookController::class);
        Route::post('/books/{book}/approve', [App\Http\Controllers\Institution\BookController::class, 'approve'])->name('books.approve');
        Route::post('/books/{book}/toggle-stock', [App\Http\Controllers\Institution\BookController::class, 'toggleStock'])->name('books.toggle-stock');
Route::post('/books/bulk-action', [App\Http\Controllers\Institution\BookController::class, 'bulkAction'])->name('books.bulk-action');
        
        Route::resource('shelves', App\Http\Controllers\Institution\ShelfController::class);
        
        Route::resource('members', App\Http\Controllers\Institution\MemberController::class);
        Route::post('/members/{member}/role', [App\Http\Controllers\Institution\MemberController::class, 'updateRole'])->name('members.update-role');
        Route::get('/members/export', [App\Http\Controllers\Institution\MemberController::class, 'export'])->name('members.export');
        Route::post('/members/bulk-action', [App\Http\Controllers\Institution\MemberController::class, 'bulkAction'])->name('members.bulk-action');
        Route::get('/members/{id}/json', [App\Http\Controllers\Institution\MemberController::class, 'editJson'])->name('members.json');
        Route::get('/members/trashed', [App\Http\Controllers\Institution\MemberController::class, 'trashed'])->name('members.trashed');
        Route::post('/members/{id}/restore', [App\Http\Controllers\Institution\MemberController::class, 'restore'])->name('members.restore');
        Route::delete('/members/{id}/force-delete', [App\Http\Controllers\Institution\MemberController::class, 'forceDelete'])->name('members.force-delete');
        Route::get('/members/directory', [App\Http\Controllers\Institution\MemberController::class, 'directory'])->name('members.directory');
        
        Route::get('/join-requests', [App\Http\Controllers\Institution\JoinRequestController::class, 'index'])->name('join-requests.index');
        Route::get('/join-requests/{id}', [App\Http\Controllers\Institution\JoinRequestController::class, 'show'])->name('join-requests.show');
        Route::post('/join-requests/{id}/approve', [App\Http\Controllers\Institution\JoinRequestController::class, 'approve'])->name('join-requests.approve');
        Route::post('/join-requests/{id}/reject', [App\Http\Controllers\Institution\JoinRequestController::class, 'reject'])->name('join-requests.reject');
        
        Route::get('/borrowings', [App\Http\Controllers\Institution\BorrowingController::class, 'index'])->name('borrowings.index');
        Route::get('/borrowings/create', [App\Http\Controllers\Institution\BorrowingController::class, 'create'])->name('borrowings.create');
        Route::post('/borrowings', [App\Http\Controllers\Institution\BorrowingController::class, 'store'])->name('borrowings.store');
        Route::get('/borrowings/{borrowing}', [App\Http\Controllers\Institution\BorrowingController::class, 'show'])->name('borrowings.show');
        Route::post('/borrowings/{borrowing}/return', [App\Http\Controllers\Institution\BorrowingController::class, 'returnBook'])->name('borrowings.return');
        Route::delete('/borrowings/{borrowing}', [App\Http\Controllers\Institution\BorrowingController::class, 'destroy'])->name('borrowings.destroy');
        
        Route::resource('withdrawals', App\Http\Controllers\Institution\WithdrawalController::class);
        Route::post('/withdrawals/{withdrawal}/cancel', [App\Http\Controllers\Institution\WithdrawalController::class, 'cancel'])->name('withdrawals.cancel');
        Route::get('/withdrawals/export', [App\Http\Controllers\Institution\WithdrawalController::class, 'export'])->name('withdrawals.export');
        
        Route::get('/reports', [App\Http\Controllers\Institution\ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/export', [App\Http\Controllers\Institution\ReportController::class, 'export'])->name('reports.export');
        
        Route::get('/settings', [App\Http\Controllers\Institution\SettingController::class, 'index'])->name('settings');
        Route::put('/settings', [App\Http\Controllers\Institution\SettingController::class, 'update'])->name('settings.update');
        
        Route::get('/subscription', [InstitutionSubscriptionController::class, 'index'])->name('subscription.index');
        Route::post('/subscription/extend', [InstitutionSubscriptionController::class, 'extend'])->name('subscription.extend');
        Route::get('/subscription/history', [InstitutionSubscriptionController::class, 'history'])->name('subscription.history');
        Route::post('/subscription/cancel', [InstitutionSubscriptionController::class, 'cancel'])->name('subscription.cancel');
    });

// ==========================================
// SECTION 5: LIBRARIAN ROUTES
// ==========================================
Route::middleware(['auth', \App\Http\Middleware\LibrarianMiddleware::class])
    ->prefix('librarian')
    ->name('librarian.')
    ->group(function () {
        
        Route::get('/dashboard', [App\Http\Controllers\Librarian\DashboardController::class, 'index'])->name('dashboard');
        
        Route::resource('books', App\Http\Controllers\Librarian\BookController::class);
        Route::post('/books/{book}/approve', [App\Http\Controllers\Librarian\BookController::class, 'approve'])->name('books.approve');
        Route::post('/books/{book}/reject', [App\Http\Controllers\Librarian\BookController::class, 'reject'])->name('books.reject');
        
        Route::resource('shelves', App\Http\Controllers\Librarian\ShelfController::class);
        
        Route::get('/members', [App\Http\Controllers\Librarian\MemberController::class, 'index'])->name('members.index');
        Route::get('/members/{member}', [App\Http\Controllers\Librarian\MemberController::class, 'show'])->name('members.show');
        Route::post('/members/{member}/role', [App\Http\Controllers\Librarian\MemberController::class, 'updateRole'])->name('members.update-role');
        Route::delete('/members/{member}', [App\Http\Controllers\Librarian\MemberController::class, 'destroy'])->name('members.destroy');
        Route::get('/members/export', [App\Http\Controllers\Librarian\MemberController::class, 'export'])->name('members.export');
        
        Route::get('/reports', [App\Http\Controllers\Librarian\ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/export', [App\Http\Controllers\Librarian\ReportController::class, 'export'])->name('reports.export');
        
        Route::get('/join-requests', [App\Http\Controllers\Librarian\JoinRequestController::class, 'index'])->name('join-requests');
        Route::post('/join-requests/{joinRequest}/approve', [App\Http\Controllers\Librarian\JoinRequestController::class, 'approve'])->name('join-requests.approve');
        Route::post('/join-requests/{joinRequest}/reject', [App\Http\Controllers\Librarian\JoinRequestController::class, 'reject'])->name('join-requests.reject');
        
        Route::get('/settings', [App\Http\Controllers\Librarian\SettingController::class, 'index'])->name('settings');
        Route::put('/settings', [App\Http\Controllers\Librarian\SettingController::class, 'update'])->name('settings.update');
        
        Route::get('/borrowings', [App\Http\Controllers\Librarian\BorrowingController::class, 'index'])->name('borrowings.index');
        Route::get('/borrowings/create', [App\Http\Controllers\Librarian\BorrowingController::class, 'create'])->name('borrowings.create');
        Route::post('/borrowings', [App\Http\Controllers\Librarian\BorrowingController::class, 'store'])->name('borrowings.store');
        Route::get('/borrowings/{borrowing}', [App\Http\Controllers\Librarian\BorrowingController::class, 'show'])->name('borrowings.show');
        Route::post('/borrowings/{borrowing}/return', [App\Http\Controllers\Librarian\BorrowingController::class, 'returnBook'])->name('borrowings.return');
        Route::delete('/borrowings/{borrowing}', [App\Http\Controllers\Librarian\BorrowingController::class, 'destroy'])->name('borrowings.destroy');
    });

// ==========================================
// SECTION 6: SELLER ROUTES
// ==========================================
Route::middleware(['auth', 'role:author|bookseller'])->prefix('seller')->name('seller.')->group(function () {
    Route::get('/dashboard', [SellerController::class, 'dashboard'])->name('dashboard');
    Route::get('/listings', [SellerController::class, 'listings'])->name('listings');
    Route::get('/orders', [SellerController::class, 'orders'])->name('orders');
    Route::get('/earnings', [SellerController::class, 'earnings'])->name('earnings');
});

// ==========================================
// SECTION 7: MARKETPLACE ROUTES
// ==========================================
Route::middleware(['auth', 'role:author|bookseller'])->prefix('marketplace')->name('marketplace.')->group(function () {
    Route::get('/create', [MarketplaceController::class, 'create'])->name('create');
    Route::post('/store', [MarketplaceController::class, 'store'])->name('store');
    Route::get('/{listing}/edit', [MarketplaceController::class, 'edit'])->name('edit');
    Route::put('/{listing}', [MarketplaceController::class, 'update'])->name('update');
    Route::delete('/{listing}', [MarketplaceController::class, 'destroy'])->name('destroy');
    Route::get('/listings', [MarketplaceController::class, 'myListings'])->name('listings');
});

Route::post('/marketplace/{listing}/buy', [MarketplaceController::class, 'buy'])->name('marketplace.buy');
Route::get('/marketplace/{listing}/download', [MarketplaceController::class, 'download'])->name('marketplace.download');

// ==========================================
// SECTION 8: AUTHOR ROUTES
// ==========================================
Route::middleware(['auth'])->prefix('author')->name('author.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Author\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/royalties', [App\Http\Controllers\Author\RoyaltyController::class, 'index'])->name('royalties.index');
    Route::resource('books', App\Http\Controllers\Author\BookController::class);
    
    Route::prefix('withdrawals')->name('withdrawals.')->group(function () {
        Route::get('/', [App\Http\Controllers\Author\WithdrawalController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Author\WithdrawalController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Author\WithdrawalController::class, 'store'])->name('store');
    });
});

// ==========================================
// SECTION 9: INSTRUCTOR ROUTES
// ==========================================
Route::middleware(['auth'])->prefix('instructor')->name('instructor.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Instructor\DashboardController::class, 'index'])->name('dashboard');
    Route::resource('courses', App\Http\Controllers\Instructor\CourseController::class);
    Route::post('/courses/{course}/lessons', [App\Http\Controllers\Instructor\CourseController::class, 'addLesson'])->name('courses.lessons.store');
    Route::put('/lessons/{lesson}', [App\Http\Controllers\Instructor\CourseController::class, 'updateLesson'])->name('lessons.update');
    Route::delete('/lessons/{lesson}', [App\Http\Controllers\Instructor\CourseController::class, 'deleteLesson'])->name('lessons.destroy');
    Route::get('/courses/{course}/enrollments', [App\Http\Controllers\Instructor\CourseController::class, 'enrollments'])->name('courses.enrollments');
});

// ==========================================
// SECTION 10: ADMIN ROUTES
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
    
    Route::get('/applications', [AdminApplicationController::class, 'index'])->name('applications.index');
    Route::get('/applications/{application}', [AdminApplicationController::class, 'show'])->name('applications.show');
    Route::post('/applications/{application}/approve', [AdminApplicationController::class, 'approve'])->name('applications.approve');
    Route::post('/applications/{application}/reject', [AdminApplicationController::class, 'reject'])->name('applications.reject');
    Route::delete('/applications/{application}', [AdminApplicationController::class, 'destroy'])->name('applications.destroy');
    
    Route::prefix('payments')->name('payments.')->group(function () {
        Route::get('/', [AdminPaymentController::class, 'index'])->name('index');
        Route::get('/transactions', [AdminPaymentController::class, 'transactions'])->name('transactions');
        Route::get('/commissions', [AdminPaymentController::class, 'commissions'])->name('commissions');
        Route::get('/author-payouts', [AdminPaymentController::class, 'authorPayouts'])->name('author-payouts');
        Route::get('/audit-logs', [AdminPaymentController::class, 'auditLogs'])->name('audit-logs');
        Route::get('/{id}', [AdminPaymentController::class, 'show'])->name('show');
    });
    
    Route::get('/quotes', [App\Http\Controllers\QuoteController::class, 'index'])->name('quotes.index');
    Route::get('/quotes/create', [App\Http\Controllers\QuoteController::class, 'create'])->name('quotes.create');
    Route::post('/quotes', [App\Http\Controllers\QuoteController::class, 'store'])->name('quotes.store');
    Route::get('/quotes/{quote}/edit', [App\Http\Controllers\QuoteController::class, 'edit'])->name('quotes.edit');
    Route::put('/quotes/{quote}', [App\Http\Controllers\QuoteController::class, 'update'])->name('quotes.update');
    Route::delete('/quotes/{quote}', [App\Http\Controllers\QuoteController::class, 'destroy'])->name('quotes.destroy');
    Route::get('/quotes/{quote}/analytics', [App\Http\Controllers\QuoteController::class, 'analytics'])->name('quotes.analytics');
});

// ==========================================
// SECTION 11: SUPER ADMIN & MEDIA TEAM ROUTES
// ==========================================

// Media Team + Super Admin routes (Content Management)
Route::middleware(['auth', \App\Http\Middleware\MediaTeamMiddleware::class])->prefix('super-admin')->name('super-admin.')->group(function () {

    // ==========================================
    // CONTENT MANAGEMENT - Media Team & Super Admin
    // ==========================================
    
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
// MEDIA TEAM DASHBOARD ROUTE
// ==========================================
Route::middleware(['auth', \App\Http\Middleware\MediaTeamMiddleware::class])->prefix('super-admin')->name('super-admin.')->group(function () {
    
    // Media Dashboard
    Route::get('/media-dashboard', function () {
        $heroSlidesCount = \App\Models\HeroSlide::count();
        $newsItemsCount = \App\Models\NewsItem::count();
        $foundersCount = \App\Models\Founder::count();
        $siteSettingsCount = \App\Models\SiteSetting::count();
        
        return view('super-admin.media-dashboard', compact(
            'heroSlidesCount',
            'newsItemsCount',
            'foundersCount',
            'siteSettingsCount'
        ));
    })->name('media.dashboard');
    
    // ==========================================
    // CONTENT MANAGEMENT - Media Team & Super Admin
    // ==========================================
    
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
// SUPER ADMIN ONLY ROUTES
// ==========================================
Route::middleware(['auth', 'superadmin'])->prefix('super-admin')->name('super-admin.')->group(function () {

    // ==========================================
    // DASHBOARD
    // ==========================================
    Route::get('/dashboard', [App\Http\Controllers\SuperAdmin\DashboardController::class, 'index'])->name('dashboard');

    // ==========================================
    // ANALYTICS
    // ==========================================
    Route::get('/analytics', [App\Http\Controllers\SuperAdmin\AnalyticsController::class, 'index'])->name('analytics.index');
    Route::get('/analytics/data', [App\Http\Controllers\SuperAdmin\AnalyticsController::class, 'getData'])->name('analytics.data');
    Route::get('/analytics/export', [App\Http\Controllers\SuperAdmin\AnalyticsController::class, 'export'])->name('analytics.export');

    // ==========================================
    // PLATFORM MANAGEMENT
    // ==========================================
    
    // Books
    Route::resource('books', App\Http\Controllers\SuperAdmin\BookController::class);
    Route::post('/books/{book}/toggle-status', [App\Http\Controllers\SuperAdmin\BookController::class, 'toggleStatus'])->name('books.toggle-status');
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
        Route::get('/', [App\Http\Controllers\SuperAdmin\PaymentController::class, 'index'])->name('index');
        Route::get('/transactions', [App\Http\Controllers\SuperAdmin\PaymentController::class, 'transactions'])->name('transactions');
        Route::get('/withdrawals', [App\Http\Controllers\SuperAdmin\PaymentController::class, 'withdrawals'])->name('withdrawals');
        Route::get('/commissions', [App\Http\Controllers\SuperAdmin\PaymentController::class, 'commissions'])->name('commissions');
        Route::get('/author-payouts', [App\Http\Controllers\SuperAdmin\PaymentController::class, 'authorPayouts'])->name('author-payouts');
        Route::get('/audit-logs', [App\Http\Controllers\SuperAdmin\PaymentController::class, 'auditLogs'])->name('audit-logs');
        Route::get('/export', [App\Http\Controllers\SuperAdmin\PaymentController::class, 'exportReport'])->name('export');
        Route::get('/{payment}', [App\Http\Controllers\SuperAdmin\PaymentController::class, 'show'])->name('show');
        Route::post('/user-withdrawals/{id}/approve', [App\Http\Controllers\SuperAdmin\PaymentController::class, 'approveUserWithdrawal'])->name('approve-user-withdrawal');
        Route::post('/user-withdrawals/{id}/reject', [App\Http\Controllers\SuperAdmin\PaymentController::class, 'rejectUserWithdrawal'])->name('reject-user-withdrawal');
        Route::post('/institution-withdrawals/{id}/approve', [App\Http\Controllers\SuperAdmin\PaymentController::class, 'approveInstitutionWithdrawal'])->name('approve-institution-withdrawal');
        Route::post('/institution-withdrawals/{id}/complete', [App\Http\Controllers\SuperAdmin\PaymentController::class, 'completeInstitutionWithdrawal'])->name('complete-institution-withdrawal');
        Route::post('/institution-withdrawals/{id}/reject', [App\Http\Controllers\SuperAdmin\PaymentController::class, 'rejectInstitutionWithdrawal'])->name('reject-institution-withdrawal');
        Route::post('/author-payouts/{transactionId}/approve', [App\Http\Controllers\SuperAdmin\PaymentController::class, 'approveAuthorPayout'])->name('approve-author-payout');
        Route::post('/author-payouts/{transactionId}/reject', [App\Http\Controllers\SuperAdmin\PaymentController::class, 'rejectAuthorPayout'])->name('reject-author-payout');
    });
    
    Route::get('/withdrawals/{withdrawal}', [App\Http\Controllers\SuperAdmin\PaymentController::class, 'withdrawalShow'])->name('withdrawals.show');

    // Quotes
    Route::get('/quotes', [App\Http\Controllers\QuoteController::class, 'index'])->name('quotes.index');
    Route::get('/quotes/create', [App\Http\Controllers\QuoteController::class, 'create'])->name('quotes.create');
    Route::post('/quotes', [App\Http\Controllers\QuoteController::class, 'store'])->name('quotes.store');
    Route::get('/quotes/{quote}/edit', [App\Http\Controllers\QuoteController::class, 'edit'])->name('quotes.edit');
    Route::put('/quotes/{quote}', [App\Http\Controllers\QuoteController::class, 'update'])->name('quotes.update');
    Route::delete('/quotes/{quote}', [App\Http\Controllers\QuoteController::class, 'destroy'])->name('quotes.destroy');
    Route::get('/quotes/{quote}/analytics', [App\Http\Controllers\QuoteController::class, 'analytics'])->name('quotes.analytics');

    // ==========================================
    // SUBSCRIPTIONS (Super Admin Only)
    // ==========================================
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
// SECTION 12: PAYMENT WEBHOOKS CALLBACKS
// ==========================================
Route::post('/webhooks/mpesa/callback', [InstitutionSubscriptionController::class, 'mpesaCallback'])
    ->name('mpesa.callback');
Route::post('/webhooks/tigopesa/callback', [InstitutionSubscriptionController::class, 'tigopesaCallback'])
    ->name('tigopesa.callback');
Route::post('/webhooks/halopesa/callback', [InstitutionSubscriptionController::class, 'halopesaCallback'])
    ->name('halopesa.callback');
Route::get('/webhooks/pesapal/callback', [InstitutionSubscriptionController::class, 'pesapalCallback'])
    ->name('pesapal.callback');

// ==========================================
// BOOK PURCHASE ROUTES
// ==========================================
Route::middleware(['auth'])->group(function () {
    
    // Show purchase page
    Route::get('/book/purchase/{bookId}', [App\Http\Controllers\BookPurchaseController::class, 'purchase'])
        ->name('book.purchase');
    
    // Process purchase with wallet (AJAX)
    Route::post('/book/purchase/wallet/{bookId}', [App\Http\Controllers\BookPurchaseController::class, 'purchaseWithWallet'])
        ->name('book.purchase.wallet');
    
    // Process purchase with external payment
    Route::post('/book/purchase/process', [App\Http\Controllers\BookPurchaseController::class, 'processPurchase'])
        ->name('book.purchase.process');
    
    // Purchase success page
    Route::get('/book/purchase/success/{paymentId}', [App\Http\Controllers\BookPurchaseController::class, 'purchaseSuccess'])
        ->name('book.purchase.success');
    
    // Purchase history
    Route::get('/book/purchase/history', [App\Http\Controllers\BookPurchaseController::class, 'purchaseHistory'])
        ->name('book.purchase.history');
    
    // Download purchased book
    Route::get('/book/download/{bookId}', [App\Http\Controllers\BookPurchaseController::class, 'downloadBook'])
        ->name('book.download');
});


// ==========================================
// WALLET API ROUTES
// ==========================================
Route::middleware(['auth'])->group(function () {
    Route::get('/api/user/wallet-balance', function() {
        if (!auth()->check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        return response()->json([
            'balance' => auth()->user()->wallet_balance ?? 0,
            'currency' => 'TSh'
        ]);
    })->name('api.wallet.balance');
});

// ==========================================
// PAYMENT INSTRUCTIONS ROUTE
// ==========================================
Route::middleware(['auth'])->group(function () {
    Route::get('/payment/instructions/{paymentId}', [App\Http\Controllers\BookPurchaseController::class, 'showPaymentInstructions'])
        ->name('payment.instructions');
    
    Route::post('/payment/confirm', [App\Http\Controllers\BookPurchaseController::class, 'confirmPayment'])
        ->name('payment.confirm');
});

Route::post('/shelves/sync-counts', [App\Http\Controllers\Institution\ShelfController::class, 'syncCounts'])
    ->name('institution.shelves.sync-counts');
// ==========================================
// SECTION 13: AUTHENTICATION ROUTES
// ==========================================
require __DIR__.'/auth.php';