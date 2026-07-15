<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    protected string $botToken;
    protected array $chatIds;
    protected bool $notifyNewOrder;
    protected bool $notifyPaid;

    public function __construct()
    {
        $this->botToken      = config('telegram.bot_token');
        $this->chatIds       = config('telegram.chat_ids', []);
        $this->notifyNewOrder = config('telegram.notify_new_order', true);
        $this->notifyPaid     = config('telegram.notify_paid', true);
    }

    public function isConfigured(): bool
    {
        return !empty($this->botToken) && !empty($this->chatIds);
    }

    /**
     * Send a plain text message to all configured chats.
     */
    public function sendMessage(string $text, string $parseMode = 'HTML'): bool
    {
        if (!$this->isConfigured()) {
            return false;
        }

        $success = true;

        foreach ($this->chatIds as $chatId) {
            try {
                $response = Http::timeout(10)->post(
                    "https://api.telegram.org/bot{$this->botToken}/sendMessage",
                    [
                        'chat_id'    => $chatId,
                        'text'       => $text,
                        'parse_mode' => $parseMode,
                    ]
                );

                if (!$response->successful()) {
                    Log::error('Telegram sendMessage failed', [
                        'chat_id' => $chatId,
                        'status'  => $response->status(),
                        'body'    => $response->body(),
                    ]);
                    $success = false;
                }
            } catch (\Exception $e) {
                Log::error('Telegram sendMessage exception', [
                    'chat_id' => $chatId,
                    'error'   => $e->getMessage(),
                ]);
                $success = false;
            }
        }

        return $success;
    }

    /**
     * Notify admin: new order created (pending payment).
     */
    public function notifyNewOrder($order): bool
    {
        if (!$this->notifyNewOrder) return false;

        $items = $order->items->map(function ($item) {
            $qty = $item->quantity > 1 ? " x{$item->quantity}" : '';
            return "  • {$item->product_name} ({$item->variant_name}){$qty} — Rp " . number_format($item->subtotal, 0, ',', '.');
        })->implode("\n");

        $url = url(route('admin.orders.show', $order->id));

        $text = "🆕 <b>Pesanan Baru</b>\n"
            . "━━━━━━━━━━━━━━━━\n"
            . "📋 <code>{$order->order_number}</code>\n"
            . "👤 {$order->customer_name}\n"
            . "📧 {$order->customer_email}\n"
            . "━━━━━━━━━━━━━━━━\n"
            . "{$items}\n"
            . "━━━━━━━━━━━━━━━━\n"
            . "💰 Total: <b>Rp " . number_format($order->total_amount, 0, ',', '.') . "</b>\n"
            . "⏳ Status: Menunggu Pembayaran\n"
            . "\n🔗 <a href=\"{$url}\">Lihat Pesanan</a>";

        return $this->sendMessage($text);
    }

    /**
     * Notify admin: order has been paid.
     */
    public function notifyPaid($order): bool
    {
        if (!$this->notifyPaid) return false;

        $url = url(route('admin.orders.show', $order->id));

        $text = "✅ <b>Pesanan Dibayar</b>\n"
            . "━━━━━━━━━━━━━━━━\n"
            . "📋 <code>{$order->order_number}</code>\n"
            . "👤 {$order->customer_name}\n"
            . "💰 Total: <b>Rp " . number_format($order->total_amount, 0, ',', '.') . "</b>\n"
            . "🏦 Metode: QRIS\n"
            . "📅 " . ($order->paid_at ? $order->paid_at->format('d M Y H:i') : now()->format('d M Y H:i'))
            . "\n\n🔗 <a href=\"{$url}\">Lihat Pesanan</a>";

        return $this->sendMessage($text);
    }
}
