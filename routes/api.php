<?php

use App\Http\Controllers\Api\Admin\AuctionController as AdminAuctionController;
use App\Http\Controllers\Api\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Api\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Api\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\Api\Customer\AuctionController as CustomerAuctionController;
use App\Http\Controllers\Api\Customer\BidController;
use App\Http\Controllers\Api\Customer\DashboardController as CustomerDashboardController;
use App\Http\Controllers\Api\Customer\OrderController as CustomerOrderController;
use App\Http\Controllers\Api\Customer\PaymentController;
use App\Http\Controllers\Api\Customer\ReminderController;
use App\Http\Controllers\Api\Public\AuctionController as PublicAuctionController;
use App\Http\Controllers\Api\Public\CategoryController as PublicCategoryController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\UserMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:60,1')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::middleware('auth:sanctum')->post('logout', [AuthController::class, 'logout']);

    Route::middleware(['signed'])->get('/email/verify/{id}/{hash}', [AuthController::class, 'VerfiyUserByLink'])->name('verification.verify');

    Route::middleware('auth:sanctum')->post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return response()->json(['message' => 'Verification email sent']);
    })->name('verification.send');

    Route::patch('public/auctions/{id}/increment-view', [PublicAuctionController::class, 'incrementViewCount']);
    Route::get('auctions/latest', [PublicAuctionController::class, 'getLatestApprovedAuctions']);
    Route::get('auctions', [PublicAuctionController::class, 'index']);
    Route::get('public/auctions/{id}', [PublicAuctionController::class, 'show']);
    Route::get('public/auction/filter', [PublicAuctionController::class, 'search']);
    Route::get('auction/{id}/related', [PublicAuctionController::class, 'getRelatedAuctions']);
    Route::get('public/auctions/{id}/winner-highest-bid', [PublicAuctionController::class, 'getWinnerAndHighestBid']);
    Route::get('category', [PublicCategoryController::class, 'index']);

    Route::middleware(['auth:sanctum', UserMiddleware::class])->group(function () {
        Route::post('user/auction/create', [CustomerAuctionController::class, 'store']);
        Route::post('user/auction/edit/{id}', [CustomerAuctionController::class, 'update']);
        Route::delete('user/auction/delete/{id}', [CustomerAuctionController::class, 'destroy']);
        Route::post('user/auction/{id}/join', [CustomerAuctionController::class, 'joinAuction']);
        Route::get('user/auctions', [CustomerAuctionController::class, 'getUserAuctions']);
        Route::get('user/auction/{id}', [CustomerAuctionController::class, 'getUserAuction']);
        Route::get('/user/profile', [AuthController::class, 'getAuthenticatedUser']);
        Route::post('user/reminders', [ReminderController::class, 'add']);
        Route::get('user/reminders', [ReminderController::class, 'index']);
        Route::delete('user/reminders/{id}', [ReminderController::class, 'delete']);
        Route::post('user/generate-payment-links ', [PaymentController::class, 'generatePaymentLinks']);
        Route::get('user/auction/{id}/live', [CustomerAuctionController::class, 'getLiveAuctionDetails']);
        Route::post('user/auction/{id}/bids', [BidController::class, 'placeBid']);
        Route::get('user/auction/{id}/bids', [CustomerAuctionController::class, 'getBidsAndViewers']);
        Route::get('user/orders', action: [CustomerOrderController::class, 'getUserOrders']);
        Route::get('user/dashboard/stats', [CustomerDashboardController::class, 'getUserDashboardStats']);
        Route::get('user/dashboard/orders', [CustomerDashboardController::class, 'getUserOrders']);
    });
    Route::middleware(['auth:sanctum', AdminMiddleware::class])->group(function () {
        Route::get('admin/auctions/pending', [AdminAuctionController::class, 'pendingAuctions']);
        Route::post('admin/auctions/{id}/approve', [AdminAuctionController::class, 'approveAuction']);
        Route::post('admin/auctions/{id}/reject', [AdminAuctionController::class, 'rejectAuction']);
        Route::get('admin/categories', [AdminCategoryController::class, 'index']);
        Route::post('admin/categories', [AdminCategoryController::class, 'store']);
        Route::delete('admin/categories/{id}', [AdminCategoryController::class, 'destroy']);
        Route::get('admin/recent-orders', [AdminOrderController::class, 'getRecentOrders']);
        Route::get('admin/recent-users', [AdminDashboardController::class, 'getRecentUsers']);
        Route::get('admin/statistics', [AdminDashboardController::class, 'getStatistics']);
    });
});
