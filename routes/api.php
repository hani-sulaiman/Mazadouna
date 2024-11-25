<?php

use App\Http\Controllers\api\AdminCategoryController;
use App\Http\Controllers\api\AdminController;
use App\Http\Controllers\api\AdminDashboardController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\AuctionController;
use App\Http\Controllers\api\BidController;
use App\Http\Controllers\api\CategoryController;
use App\Http\Controllers\api\DashboardController;
use App\Http\Controllers\api\OrderController;
use App\Http\Controllers\api\ReminderController;
use App\Http\Controllers\api\PaymentController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\UserMiddleware;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('logout', [AuthController::class, 'logout']);
Route::patch('public/auctions/{id}/increment-view', [AuctionController::class, 'incrementViewCount']);
Route::get('auctions/latest', [AuctionController::class, 'getLatestApprovedAuctions']);
Route::get('auctions', [AuctionController::class, 'index']);
Route::get('public/auctions/{id}', [AuctionController::class, 'show']);
Route::get('public/auction/filter', [AuctionController::class, 'search']);
Route::get('auction/{id}/related', [AuctionController::class, 'getRelatedAuctions']);
Route::get('public/auctions/{id}/winner-highest-bid', [AuctionController::class, 'getWinnerAndHighestBid']);
Route::get('category', [CategoryController::class, 'index']);
Route::middleware(['auth:sanctum', UserMiddleware::class])->group(function () {
    Route::post('user/auction/create', [AuctionController::class, 'store']);
    Route::post('user/auction/edit/{id}', [AuctionController::class, 'update']);
    Route::delete('user/auction/delete/{id}', [AuctionController::class, 'destroy']);
    Route::post('user/auction/{id}/join', [AuctionController::class, 'joinAuction']);
    Route::get('user/auctions', [AuctionController::class, 'getUserAuctions']);
    Route::get('user/auction/{id}', [AuctionController::class, 'getUserAuction']);
    Route::get('/user/profile', [AuthController::class, 'getAuthenticatedUser']);

    Route::post('user/reminders', [ReminderController::class, 'add']);        // Add a reminder
    Route::get('user/reminders', [ReminderController::class, 'index']);       // View all reminders
    Route::delete('user/reminders/{id}', [ReminderController::class, 'delete']);
    Route::post('user/generate-payment-links ', [PaymentController::class, 'generatePaymentLinks']);
    Route::get('user/auction/{id}/live', [AuctionController::class, 'getLiveAuctionDetails']);
    Route::post('user/auction/{id}/bids', [BidController::class, 'placeBid']);
    Route::get('user/auction/{id}/bids', [AuctionController::class, 'getBidsAndViewers']);
    Route::get('user/orders', [OrderController::class, 'getUserOrders']);
    Route::get('user/dashboard/stats', [DashboardController::class, 'getUserDashboardStats']);
    Route::get('user/dashboard/orders', [DashboardController::class, 'getUserOrders']);
});
Route::middleware(['auth:sanctum', AdminMiddleware::class])->group(function () {
    Route::get('admin/auctions/pending', [AuctionController::class, 'pendingAuctions']); // قائمة المزادات التي تنتظر الموافقة
    Route::post('admin/auctions/{id}/approve', [AuctionController::class, 'approveAuction']); // الموافقة على المزاد
    Route::post('admin/auctions/{id}/reject', [AuctionController::class, 'rejectAuction']); // رفض المزاد

    Route::get('admin/categories', [AdminCategoryController::class, 'index']);
    Route::post('admin/categories', [AdminCategoryController::class, 'store']);    // Create category
    Route::delete('admin/categories/{id}', [AdminCategoryController::class, 'destroy']); // Delete category
    
    Route::get('admin/recent-orders', [AdminDashboardController::class, 'getRecentOrders']);
    Route::get('admin/recent-users', [AdminDashboardController::class, 'getRecentUsers']);
    Route::get('admin/statistics', [AdminDashboardController::class, 'getStatistics']);
});

Route::get('/run-scheduled-task', function () {
    Artisan::call('auction:determine-winners');
    return response()->json(['status' => 'Task run successfully']);
});