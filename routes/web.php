<?php

use App\Http\Controllers\Auth\SetPasswordController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\TelegramBotController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// ===== TELEGRAM BOT WEBHOOK =====
Route::post('/telegram/webhook', [TelegramBotController::class, 'webhook'])
    ->name('telegram.webhook');

// ===== SHOP ROUTES =====
Route::get('/', [ShopController::class, 'home'])->name('shop.home');
Route::get('/kategori/{slug}', [ShopController::class, 'category'])->name('shop.category');
Route::get('/produk', [ShopController::class, 'products'])->name('shop.products');
Route::get('/produk/{slug}', [ShopController::class, 'product'])->name('shop.product');
Route::middleware(['auth'])->group(function () {
    Route::post('/checkout', [ShopController::class, 'checkout'])->name('shop.checkout');
});
Route::get('/pesanan/{orderNumber}', [ShopController::class, 'order'])->name('shop.order');
Route::get('/lacak', function () {
    return Inertia::render('Shop/Track');
})->name('shop.track');
Route::post('/lacak', [ShopController::class, 'trackOrder'])->name('shop.track.submit');

// ===== SET PASSWORD (Google users) =====
Route::middleware(['auth'])->group(function () {
    Route::get('/atur-password', [SetPasswordController::class, 'show'])->name('password.set');
    Route::post('/atur-password', [SetPasswordController::class, 'store'])->name('password.set.store');
});

// ===== USER DASHBOARD =====
Route::middleware(['auth', 'password.set'])->prefix('dashboard')->name('dashboard.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('index');
    Route::get('/pesanan', [DashboardController::class, 'orders'])->name('orders');
    Route::get('/profil', [DashboardController::class, 'profile'])->name('profile');
    Route::patch('/profil', [DashboardController::class, 'updateProfile'])->name('profile.update');
    Route::put('/password', [DashboardController::class, 'updatePassword'])->name('password.update');
});

Route::middleware(['auth', 'password.set'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
require __DIR__ . '/admin.php';
