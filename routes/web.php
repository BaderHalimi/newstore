<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\Auth\CustomerAuthController;
use App\Http\Controllers\Auth\GoogleAuthController;

// Shop Routes
Route::get('/', [ShopController::class, 'index'])->name('shop.index');
Route::get('/product/{product:slug}', [ShopController::class, 'show'])->name('shop.show');
Route::get('/category/{category:slug}', [ShopController::class, 'category'])->name('shop.category');

// Test Auth Page
Route::get('/test-auth', function () {
    return view('test-auth');
})->name('test.auth');

// Cart Routes
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
Route::patch('/cart/update/{cartItem}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/remove/{cartItem}', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

// Checkout Routes
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/checkout/success/{order}', [CheckoutController::class, 'success'])->name('checkout.success');

// Customer Auth Routes
Route::prefix('auth')->group(function () {
    Route::post('/register', [CustomerAuthController::class, 'register'])->name('auth.register');
    Route::post('/verify-account', [CustomerAuthController::class, 'verifyAccount'])->name('auth.verify');
    Route::post('/resend-verification', [CustomerAuthController::class, 'resendVerification'])->name('auth.resendVerification');
    Route::post('/send-code', [CustomerAuthController::class, 'sendLoginCode'])->name('auth.sendCode');
    Route::post('/login-code', [CustomerAuthController::class, 'loginWithCode'])->name('auth.loginCode');
    Route::post('/login-password', [CustomerAuthController::class, 'loginWithPassword'])->name('auth.loginPassword');
    Route::post('/logout', [CustomerAuthController::class, 'logout'])->name('auth.logout');

    // Google OAuth
    Route::get('/google', [GoogleAuthController::class, 'redirect'])->name('auth.google');
    Route::get('/google/callback', [GoogleAuthController::class, 'callback'])->name('auth.google.callback');
});

// Auth Routes (للعملاء - بسيط)
Route::middleware(['auth'])->group(function () {
    Route::get('/my-orders', function () {
        $orders = auth()->user()->orders()->with('items.product')->latest()->get();
        return view('account.orders', compact('orders'));
    })->name('account.orders');

    Route::get('/my-orders/{order}', function (\App\Models\Order $order) {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }
        $order->load('items.product');
        return view('account.order-details', compact('order'));
    })->name('account.order-details');
});
