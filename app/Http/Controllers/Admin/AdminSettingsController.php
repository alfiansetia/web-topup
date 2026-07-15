<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
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
                'webhook_url'      => url('/telegram/webhook'),
                'webhook_info'     => $this->getWebhookInfo(),
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

    public function getTelegramWebhookInfo()
    {
        $info = $this->getWebhookInfo();

        if ($info === null) {
            return back()->with('error', 'Gagal mengambil info webhook. Cek TELEGRAM_BOT_TOKEN.');
        }

        return back()->with('webhook_info', $info);
    }

    public function setTelegramWebhook()
    {
        $botToken = config('telegram.bot_token');
        $webhookUrl = url('/telegram/webhook');
        $secret = config('telegram.webhook_secret');

        if (!$botToken) {
            return back()->with('error', 'TELEGRAM_BOT_TOKEN belum dikonfigurasi.');
        }

        $payload = ['url' => $webhookUrl];
        if ($secret) {
            $payload['secret_token'] = $secret;
        }

        try {
            $response = Http::timeout(15)->post(
                "https://api.telegram.org/bot{$botToken}/setWebhook",
                $payload
            );

            if ($response->successful() && ($response->json('ok') ?? false)) {
                return back()->with('success', "Webhook berhasil diset ke: {$webhookUrl}");
            }

            return back()->with('error', 'Gagal set webhook: ' . ($response->json('description') ?? 'Unknown error'));
        } catch (\Exception $e) {
            Log::error('Set webhook failed', ['error' => $e->getMessage()]);
            return back()->with('error', 'Gagal set webhook: ' . $e->getMessage());
        }
    }

    public function deleteTelegramWebhook()
    {
        $botToken = config('telegram.bot_token');

        if (!$botToken) {
            return back()->with('error', 'TELEGRAM_BOT_TOKEN belum dikonfigurasi.');
        }

        try {
            $response = Http::timeout(15)->post(
                "https://api.telegram.org/bot{$botToken}/deleteWebhook"
            );

            if ($response->successful() && ($response->json('ok') ?? false)) {
                return back()->with('success', 'Webhook berhasil dihapus.');
            }

            return back()->with('error', 'Gagal menghapus webhook.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus webhook: ' . $e->getMessage());
        }
    }

    private function getWebhookInfo(): ?array
    {
        $botToken = config('telegram.bot_token');

        if (!$botToken) {
            return null;
        }

        try {
            $response = Http::timeout(15)->get(
                "https://api.telegram.org/bot{$botToken}/getWebhookInfo"
            );

            if ($response->successful() && ($response->json('ok') ?? false)) {
                return $response->json('result');
            }

            return null;
        } catch (\Exception $e) {
            Log::error('getWebhookInfo failed', ['error' => $e->getMessage()]);
            return null;
        }
    }
}
