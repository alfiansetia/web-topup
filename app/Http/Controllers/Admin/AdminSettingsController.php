<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminSettingsController extends Controller
{
    public function index()
    {
        $telegramConfigured = config('telegram.bot_token') && !empty(config('telegram.chat_ids'));

        $isProduction = config('payment.pakasir_is_production', false);

        return Inertia::render('Admin/Settings/Index', [
            'telegram' => [
                'configured'      => $telegramConfigured,
                'bot_username'     => config('telegram.bot_username'),
                'notify_new_order' => config('telegram.notify_new_order', true),
                'notify_paid'      => config('telegram.notify_paid', true),
                'chat_ids'         => config('telegram.chat_ids', []),
            ],
            'pakasir' => [
                'slug'          => config('payment.pakasir_slug'),
                'secret_set'    => !empty(config('payment.pakasir_secret_key')),
                'is_production' => $isProduction,
                'webhook_url'   => url('/api/webhook/pakasir'),
            ],
        ]);
    }

    public function testTelegram(TelegramService $telegram)
    {
        if (!$telegram->isConfigured()) {
            return back()->with('error', 'Telegram belum dikonfigurasi. Isi TELEGRAM_BOT_TOKEN dan TELEGRAM_CHAT_IDS di .env');
        }

        $appName = config('app.name', 'TopUp Store');
        $text = "🧪 <b>Test Notification</b>\n"
            . "━━━━━━━━━━━━━━━━\n"
            . "Ini adalah pesan test dari <b>{$appName}</b>.\n"
            . "Jika Anda menerima pesan ini, konfigurasi Telegram sudah benar ✅\n"
            . "📅 " . now()->format('d M Y H:i:s');

        $result = $telegram->sendMessage($text);

        if ($result) {
            return back()->with('success', 'Pesan test berhasil dikirim ke Telegram!');
        }

        return back()->with('error', 'Gagal mengirim pesan test. Cek log Laravel untuk detail.');
    }
}
