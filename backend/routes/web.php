<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Home\HomepageController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ViewUserController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\OpenAIController;

Route::get('/', HomepageController::class)->name('home');
Route::get('/faq', [HomepageController::class, 'faq'])->name('faq');
Route::get('/faq-chat', fn() => view('chat.faq'))->name('faq.chat');
Route::get('/partners', [HomepageController::class, 'partners'])->name('partners');
Route::get('/announcements', [AnnouncementController::class, 'index'])->name('marketplace.index');

// Policy routes
Route::get('/terms_conditions', fn() => view('policies.terms'))->name('terms');
Route::get('/privacy_policy', fn() => view('policies.privacy'))->name('privacy');
Route::get('/accessibility', fn() => view('policies.accessibility'))->name('accessibility');
Route::get('/cookie_policy', fn() => view('policies.cookies'))->name('cookies');

Route::get('/announcements/{announcement:slug}', [AnnouncementController::class, 'show'])->name('marketplace.show');

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/sign_up', [AuthController::class, 'showSignupForm'])->name('signup');
Route::post('/signup', [AuthController::class, 'signup']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/user_dashboard', [UserDashboardController::class, 'index'])->name('user.dashboard');
    Route::get('/profile', [UserDashboardController::class, 'profile'])->name('user.profile');
    Route::put('/profile', [UserDashboardController::class, 'updateProfile'])->name('user.profile.update');
    Route::get('/my-listings', [UserDashboardController::class, 'listings'])->name('user.listings');
    
    // Chat routes
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/{conversation:slug}', [ChatController::class, 'show'])->name('chat.show');
    Route::post('/chat/{conversation:slug}/messages', [ChatController::class, 'sendMessage'])->name('chat.messages.send');
    Route::put('/chat/{conversation:slug}/read', [ChatController::class, 'markAsRead'])->name('chat.read');
    Route::post('/announcements/{announcement:slug}/conversation', [ChatController::class, 'getOrCreateConversation'])->name('chat.get-or-create');

    // Announcement creation/editing
    Route::get('/add_announcement', [AnnouncementController::class, 'create'])->name('user.announcements.create');
    Route::post('/announcements', [AnnouncementController::class, 'store'])->name('user.announcements.store');
    Route::get('/announcements/{announcement:slug}/edit', [AnnouncementController::class, 'edit'])->name('user.announcements.edit');
    Route::put('/announcements/{announcement:slug}', [AnnouncementController::class, 'update'])->name('user.announcements.update');
    Route::delete('/announcements/{announcement:slug}', [AnnouncementController::class, 'destroy'])->name('user.announcements.destroy');
    Route::put('/announcements/{announcement:slug}/status', [AnnouncementController::class, 'updateStatus'])->name('user.announcements.update-status');
    Route::post('/announcements/{announcement:slug}/favorite', [AnnouncementController::class, 'toggleFavorite'])->name('user.announcements.favorite');

    // Reviews & Offers
    Route::post('/announcements/{announcement:slug}/reviews', [AnnouncementController::class, 'storeReview'])->name('reviews.store');
    Route::post('/announcements/{announcement:slug}/offers', [AnnouncementController::class, 'makeOffer'])->name('offers.store');

    // Media upload routes
    Route::post('/media/upload', [MediaController::class, 'upload'])->name('media.upload');
    Route::post('/media/upload-multiple', [MediaController::class, 'uploadMultiple'])->name('media.upload-multiple');
    Route::delete('/media/temporary/{mediaId}', [MediaController::class, 'deleteTemporary'])->name('media.delete-temporary');

     // Admin routes
    Route::prefix('admin')->name('admin.')->middleware(['admin'])->group(function () {
        Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
        Route::get('/reports', [AdminController::class, 'reports'])->name('reports');
        Route::get('/users', [AdminController::class, 'users'])->name('users');
        Route::get('/announcements', [AdminController::class, 'announcements'])->name('announcements');
        
        // Admin stats/charts
        Route::get('/stats/funnel', [AdminController::class, 'getAnnouncementFunnel'])->name('stats.funnel');
        Route::get('/stats/categories', [AdminController::class, 'getTopCategories'])->name('stats.categories');
        Route::get('/moderation/pending', [AdminController::class, 'getPendingModeration'])->name('moderation.pending');
        
        // User management
        Route::get('/user-management/users', [ViewUserController::class, 'getViewUsers'])->name('users.view');
        Route::put('/user-management/users/{id}', [ViewUserController::class, 'updateUser'])->name('users.update');
        Route::delete('/user-management/users/{id}', [ViewUserController::class, 'deleteUser'])->name('users.destroy');
    });

    // Reports routes
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/all', [ReportController::class, 'all'])->name('all');
        Route::get('/users', [ReportController::class, 'users'])->name('users');
        Route::get('/sales', [ReportController::class, 'sales'])->name('sales');
        
        // CSV Reports
        Route::get('/csv/users', [ReportsController::class, 'usersReport'])->name('csv.users');
    });
});

// OpenAI FAQ
Route::post('/ask-faq', [OpenAIController::class, 'ask'])->name('ask-faq');

// Fallback for SPA (if needed)
Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');
