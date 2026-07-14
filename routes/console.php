<?php

use App\Commands\CancelExpiredOrders;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Auto-cancel expired orders (Pakasir QRIS = 1 jam)
Schedule::command(CancelExpiredOrders::class)->everyFiveMinutes();
