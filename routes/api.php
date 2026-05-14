<?php

use App\Http\Controllers\Api\ABAController;
use App\Http\Controllers\Api\AdminDashboardController;
use App\Http\Controllers\Api\AdminUserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\OrderController;
// use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\WebhookController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\StockLogController;
use Illuminate\Support\Facades\Route;

// ======= Authentication =======
Route::prefix('v1')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
        Route::get('/me', [AuthController::class, 'me'])->middleware('auth:sanctum');

        Route::post('/forgot-password',[PasswordController::class,'forgotPassword'])->middleware('throttle:5,1')->name('password.sent');
        Route::post('/reset-password',[PasswordController::class,'resetPassword'])->middleware('throttle:5,1')->name('password.reset');

    });
});

// Public routes
Route::prefix('v1')->group(function () {
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/low-stock', [ProductController::class, 'lowStock']);
});

Route::prefix('v1')->middleware('auth:sanctum')->group(function () {

    // ======= Role (Admin Only) =======
    Route::apiResource('/roles', RoleController::class)->middleware('admin');

    // ======= Admin Users (Admin Only) =======
    Route::prefix('admin')->middleware('admin')->group(function () {
        Route::post('/users', [AdminUserController::class, 'store']);
        Route::get('/users', [AdminUserController::class, 'index']);
        Route::get('/users/{id}', [AdminUserController::class, 'show']);
        Route::put('/users/{id}', [AdminUserController::class, 'update']);
        Route::delete('/users/{id}', [AdminUserController::class, 'destroy']);
        Route::put('/users/{id}/role', [AdminUserController::class, 'updateRole']);
        Route::put('/users/{id}/status', [AdminUserController::class, 'updateStatus']);
        Route::put('/users/{id}/reset-password', [AdminUserController::class, 'resetPassword']);

        // ======= Admin Dashboard =======
        Route::get('/dashboard/overview', [AdminDashboardController::class, 'overview']);
        Route::get('/dashboard/sales', [AdminDashboardController::class, 'sales']);
        Route::get('/dashboard/top-products', [AdminDashboardController::class, 'topProducts']);
        Route::get('/dashboard/low-stock', [AdminDashboardController::class, 'lowStockProducts']);
        Route::get('/dashboard/recent-orders', [AdminDashboardController::class, 'recentOrders']);
        Route::get('/dashboard/order-stats', [AdminDashboardController::class, 'orderStats']);
        Route::get('/dashboard/payment-stats', [AdminDashboardController::class, 'paymentStats']);
        Route::get('/dashboard/stock-movement', [AdminDashboardController::class, 'stockMovement']);
    });

    // ======= Products (Admin Only for create/update/delete) =======
    Route::post('/products', [ProductController::class, 'store'])->middleware('admin');
    Route::put('/products/{id}', [ProductController::class, 'update'])->middleware('admin');
    Route::delete('/products/{id}', [ProductController::class, 'destroy'])->middleware('admin');
    Route::get('/products/{id}', [ProductController::class, 'show']);

    // ======= Stock Logs (Admin Only) =======
    Route::post('/stocks', [StockLogController::class,'store'])->middleware('admin');

    // ======= Categories (Admin Only for create/update/delete) =======
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{id}', [CategoryController::class, 'show']);
    Route::post('/categories', [CategoryController::class, 'store'])->middleware('admin');
    Route::put('/categories/{id}', [CategoryController::class, 'update'])->middleware('admin');
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->middleware('admin');

    // ======= Cart =======
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart', [CartController::class, 'store']);
    Route::put('/cart/{id}', [CartController::class, 'update']);
    Route::delete('/cart/{id}', [CartController::class, 'destroy']);
    Route::delete('/clear/cart', [CartController::class, 'clear']);

    // ======= Orders =======
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::get('/users/{id}/orders', [OrderController::class, 'getOrdersByUserID']);

    // ======= Checkout =======
    Route::post('/checkout', [CheckoutController::class, 'checkout']);
    Route::post('/aba-checkout', [CheckoutController::class, 'abaCheckout']);
    Route::post('/payway-checkout', [CheckoutController::class, 'PayWayCheckout']);
    Route::post('/payway-check-status', [CheckoutController::class, 'checkPayWayStatus']);

    // ======= ABA =======
    Route::get('/check-transaction/{tran_id}', [ABAController::class, 'checkTransaction']);
    Route::get('/transaction-detail/{tran_id}', [ABAController::class, 'getTransactionDetail']);
    Route::get('/transaction-list', [ABAController::class, 'getTransactionsList']);
    Route::get('/close-transaction/{tran_id}', [ABAController::class, 'closeTransaction']);
    Route::get('/exchange-rate', [ABAController::class, 'getExchangeRate']);
    Route::post('/transactions/{tran_id}/refund', [ABAController::class, 'refundTransaction']);

    // ======= KHPAY =======
    Route::post('/generateQr',[CheckoutController::class,'generateKHQR']);

    // ======= Webhook =======
    Route::post('/webhook/aba/callback', [WebhookController::class, 'handleCallback']);

});
