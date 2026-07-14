<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShopController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// ===== SHOP ROUTES =====
Route::get('/', [ShopController::class, 'home'])->name('shop.home');
Route::get('/kategori/{slug}', [ShopController::class, 'category'])->name('shop.category');
Route::get('/produk', [ShopController::class, 'products'])->name('shop.products');
Route::get('/produk/{slug}', [ShopController::class, 'product'])->name('shop.product');
Route::post('/checkout', [ShopController::class, 'checkout'])->name('shop.checkout');
Route::get('/pesanan/{orderNumber}', [ShopController::class, 'order'])->name('shop.order');
Route::get('/lacak', function () {
    return Inertia::render('Shop/Track');
})->name('shop.track');
Route::post('/lacak', [ShopController::class, 'trackOrder'])->name('shop.track.submit');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
require __DIR__ . '/admin.php';
