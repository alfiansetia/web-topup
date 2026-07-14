<?php

use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\Admin\AdminSettingsController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminVariantController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Categories
    Route::resource('categories', AdminCategoryController::class)
        ->except(['show']);

    // Products
    Route::resource('products', AdminProductController::class);

    // Variants & Items per product
    Route::prefix('products/{product}')->group(function () {
        Route::get('variants', [AdminVariantController::class, 'index'])->name('variants.index');
        Route::get('variants/create', [AdminVariantController::class, 'create'])->name('variants.create');
        Route::post('variants', [AdminVariantController::class, 'store'])->name('variants.store');
        Route::get('variants/{variant}/edit', [AdminVariantController::class, 'edit'])->name('variants.edit');
        Route::put('variants/{variant}', [AdminVariantController::class, 'update'])->name('variants.update');
        Route::delete('variants/{variant}', [AdminVariantController::class, 'destroy'])->name('variants.destroy');

        // Items (stock) per variant
        Route::get('variants/{variant}/items', [AdminVariantController::class, 'items'])->name('variants.items');
        Route::post('variants/{variant}/items', [AdminVariantController::class, 'storeItem'])->name('variants.items.store');
        Route::delete('variants/{variant}/items/{item}', [AdminVariantController::class, 'destroyItem'])->name('variants.items.destroy');
        Route::put('variants/{variant}/items/{item}/status', [AdminVariantController::class, 'updateItemStatus'])->name('variants.items.status');
    });

    // Orders
    Route::get('orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');

    // Users
    Route::get('users', [AdminUserController::class, 'index'])->name('users.index');
    Route::put('users/{user}/toggle-block', [AdminUserController::class, 'toggleBlock'])->name('users.toggle-block');
    Route::delete('users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
    Route::post('orders/{order}/verify', [AdminOrderController::class, 'verifyPayment'])->name('orders.verify');
    Route::post('orders/{order}/complete', [AdminOrderController::class, 'complete'])->name('orders.complete');
    Route::post('orders/{order}/cancel', [AdminOrderController::class, 'cancel'])->name('orders.cancel');
    Route::put('orders/{order}/notes', [AdminOrderController::class, 'updateNotes'])->name('orders.notes');

    // Settings
    Route::get('settings', [AdminSettingsController::class, 'index'])->name('settings.index');
    Route::post('settings/telegram/test', [AdminSettingsController::class, 'testTelegram'])->name('settings.telegram.test');
});
