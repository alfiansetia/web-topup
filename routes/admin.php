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
        Route::put('variants/{variant}/items/{item}', [AdminVariantController::class, 'updateItem'])->name('variants.items.update');
        Route::put('variants/{variant}/items/{item}/status', [AdminVariantController::class, 'updateItemStatus'])->name('variants.items.status');
    });

    // Orders
    Route::get('orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('orders/create', [AdminOrderController::class, 'create'])->name('orders.create');
    Route::post('orders/lookup-user', [AdminOrderController::class, 'lookupUser'])->name('orders.lookup-user');
    Route::post('orders', [AdminOrderController::class, 'store'])->name('orders.store');
    Route::get('orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');

    // Users
    Route::get('users', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('users/create', [AdminUserController::class, 'create'])->name('users.create');
    Route::post('users', [AdminUserController::class, 'store'])->name('users.store');
    Route::get('users/{user}/edit', [AdminUserController::class, 'edit'])->name('users.edit');
    Route::put('users/{user}', [AdminUserController::class, 'update'])->name('users.update');
    Route::put('users/{user}/toggle-block', [AdminUserController::class, 'toggleBlock'])->name('users.toggle-block');
    Route::delete('users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
    Route::post('orders/{order}/verify', [AdminOrderController::class, 'verifyPayment'])->name('orders.verify');
    Route::post('orders/{order}/assign-items', [AdminOrderController::class, 'assignItems'])->name('orders.assign-items');
    Route::post('orders/{order}/complete', [AdminOrderController::class, 'complete'])->name('orders.complete');
    Route::post('orders/{order}/cancel', [AdminOrderController::class, 'cancel'])->name('orders.cancel');
    Route::put('orders/{order}/notes', [AdminOrderController::class, 'updateNotes'])->name('orders.notes');
    Route::post('orders/{order}/resend-email', [AdminOrderController::class, 'resendEmail'])->name('orders.resend-email');

    // Settings
    Route::get('settings', [AdminSettingsController::class, 'index'])->name('settings.index');
    Route::post('settings/telegram/test', [AdminSettingsController::class, 'testTelegram'])->name('settings.telegram.test');
    Route::get('settings/telegram/webhook-info', [AdminSettingsController::class, 'getTelegramWebhookInfo'])->name('settings.telegram.webhook-info');
    Route::post('settings/telegram/set-webhook', [AdminSettingsController::class, 'setTelegramWebhook'])->name('settings.telegram.set-webhook');
    Route::post('settings/telegram/delete-webhook', [AdminSettingsController::class, 'deleteTelegramWebhook'])->name('settings.telegram.delete-webhook');
});
