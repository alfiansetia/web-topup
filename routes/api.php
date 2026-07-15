<?php

use App\Http\Controllers\PakasirWebhookController;
use Illuminate\Support\Facades\Route;

// ===== PAKASIR WEBHOOK (no CSRF) =====
Route::post('/webhook/pakasir', [PakasirWebhookController::class, 'handle'])->name('pakasir.webhook');
