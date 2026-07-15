<?php

namespace App\Services;

use App\Mail\TelegramVerificationCode;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use App\Services\PakasirService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class TelegramBotService
{
    protected string $botToken;
    protected string $botUsername;

    // User states for conversation flow
    private const STATE_IDLE = 'idle';
    private const STATE_WAITING_EMAIL = 'waiting_email';
    private const STATE_WAITING_CODE = 'waiting_code';
    private const STATE_WAITING_ORDER_NUMBER = 'waiting_order_number';

    public function __construct()
    {
        $this->botToken = config('telegram.bot_token');
        $this->botUsername = config('telegram.bot_username', '');
    }

    /**
     * Notify user about order status change via Telegram.
     */
    public function notifyOrderStatus(Order $order): void
    {
        $user = $order->user;
        if (!$user || !$user->telegram_id) {
            return;
        }

        $chatId = $user->telegram_id;
        $statusLabel = match ($order->status) {
            'paid'      => "💳 <b>Pembayaran Diterima!</b>\n\nPesanan <code>{$order->order_number}</code> sudah dibayar. Admin akan segera memproses pesananmu.",
            'completed' => "✅ <b>Pesanan Selesai!</b>\n\nPesanan <code>{$order->order_number}</code> telah selesai diproses.",
            'cancelled' => "❌ <b>Pesanan Dibatalkan</b>\n\nPesanan <code>{$order->order_number}</code> telah dibatalkan.",
            default     => null,
        };

        if (!$statusLabel) {
            return;
        }

        $text = $statusLabel
            . "\n\n💰 Total: <b>Rp " . number_format($order->total_amount, 0, ',', '.') . "</b>"
            . "\n\n🔗 <a href=\"" . url('/pesanan/' . $order->order_number) . "\">Lihat Pesanan</a>";

        $this->sendMessage($chatId, $text, $this->mainMenuKeyboard());
    }

    /**
     * Process incoming Telegram update.
     */
    public function handleUpdate(array $update): void
    {
        // Handle callback query dari inline keyboard
        if (isset($update['callback_query'])) {
            $this->handleCallbackQuery($update['callback_query']);
            return;
        }

        // Hanya proses pesan teks dari chat pribadi
        $message = $update['message'] ?? $update['edited_message'] ?? null;
        if (!$message || !isset($message['text'])) {
            return;
        }

        // Abaikan pesan dari group/supergroup/channel
        $chatType = $message['chat']['type'] ?? '';
        if ($chatType !== 'private') {
            return;
        }

        $chatId = (string) $message['chat']['id'];
        $text = trim($message['text']);
        $firstName = $message['chat']['first_name'] ?? 'User';

        // Cek user berdasarkan telegram_id
        $user = User::where('telegram_id', $chatId)->first();

        // Handle commands
        if (str_starts_with($text, '/')) {
            $this->handleCommand($chatId, $text, $user, $firstName);
            return;
        }

        // Handle callback berdasarkan state
        $state = $this->getState($chatId);
        $this->handleState($chatId, $text, $user, $state, $firstName);
    }

    /**
     * Handle bot commands.
     */
    protected function handleCommand(string $chatId, string $text, ?User $user, string $firstName): void
    {
        $command = strtolower(explode(' ', $text)[0]);

        switch ($command) {
            case '/start':
                $this->handleStart($chatId, $user, $firstName);
                break;

            case '/help':
                $this->sendHelp($chatId);
                break;

            case '/orders':
            case '/pesanan':
                if ($user) {
                    $this->sendOrdersList($chatId, $user);
                } else {
                    $this->sendMessage($chatId, "⚠️ Kamu belum menghubungkan akun.\nKirim /start untuk memulai.");
                }
                break;

            case '/shop':
            case '/belanja':
                if ($user) {
                    $this->sendCategories($chatId);
                } else {
                    $this->sendMessage($chatId, "⚠️ Kamu belum menghubungkan akun.\nKirim /start untuk memulai.");
                }
                break;

            case '/account':
            case '/akun':
                if ($user) {
                    $this->sendAccountInfo($chatId, $user);
                } else {
                    $this->sendMessage($chatId, "⚠️ Kamu belum menghubungkan akun.\nKirim /start untuk memulai.");
                }
                break;

            case '/unlink':
                if ($user) {
                    $this->unlinkAccount($chatId, $user);
                } else {
                    $this->sendMessage($chatId, "⚠️ Tidak ada akun yang terhubung.");
                }
                break;

            default:
                $this->sendMessage($chatId, "❓ Perintah tidak dikenal. Ketik /help untuk bantuan.");
                break;
        }
    }

    /**
     * Handle /start command.
     */
    protected function handleStart(string $chatId, ?User $user, string $firstName): void
    {
        if ($user) {
            // Sudah terhubung
            $this->setState($chatId, self::STATE_IDLE);
            $this->sendMessage(
                $chatId,
                "👋 Halo, <b>{$user->name}</b>!\n\nAkun Telegram kamu sudah terhubung.",
                $this->mainMenuKeyboard()
            );
            return;
        }

        // Simpan nama Telegram untuk registrasi nanti
        Cache::put("tg_link_{$chatId}_first_name", $firstName, now()->addMinutes(10));

        // Belum terhubung — minta email
        $this->setState($chatId, self::STATE_WAITING_EMAIL);
        $this->sendMessage(
            $chatId,
            "👋 Halo, <b>{$firstName}</b>!\n\n"
                . "Selamat datang di <b>" . config('app.name') . "</b> Bot.\n\n"
                . "Untuk menghubungkan akun, silakan kirim <b>email</b> yang terdaftar di website kami.\n"
                . "Belum punya akun? Kirim email saja, kami akan buatkan otomatis."
        );
    }

    /**
     * Handle state-based conversation flow.
     */
    protected function handleState(string $chatId, string $text, ?User $user, string $state, string $firstName): void
    {
        switch ($state) {
            case self::STATE_WAITING_EMAIL:
                $this->handleEmailInput($chatId, $text, $firstName);
                break;

            case self::STATE_WAITING_CODE:
                $this->handleCodeInput($chatId, $text);
                break;

            case self::STATE_WAITING_ORDER_NUMBER:
                $this->handleOrderNumberInput($chatId, $text, $user);
                break;

            case self::STATE_IDLE:
            default:
                if (!$user) {
                    // User belum linked, suruh /start
                    $this->sendMessage($chatId, "⚠️ Kamu belum menghubungkan akun.\nKirim /start untuk memulai.");
                    return;
                }
                // Handle menu text
                $this->handleMenuText($chatId, $text, $user);
                break;
        }
    }

    /**
     * Handle email input during linking flow.
     */
    protected function handleEmailInput(string $chatId, string $email, string $firstName): void
    {
        // Validasi format email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->sendMessage($chatId, "❌ Format email tidak valid. Coba lagi:");
            return;
        }

        $email = strtolower($email);

        // Cari user berdasarkan email
        $user = User::where('email', $email)->first();

        // User belum terdaftar — buat akun otomatis pakai nama dari Telegram
        if (!$user) {
            // Ambil nama dari cache (disimpan saat /start), fallback ke firstName
            $name = Cache::pull("tg_link_{$chatId}_first_name", $firstName);

            // Cek race condition
            $existing = User::where('email', $email)->first();
            if ($existing) {
                $user = $existing;
            } else {
                $user = User::create([
                    'name'              => $name,
                    'email'             => $email,
                    'role'              => 'user',
                    'email_verified_at' => now(),
                ]);
                // Tandai sebagai user baru untuk pesan sukses
                Cache::put("tg_link_{$chatId}_new_user", true, now()->addMinutes(10));
            }
        }

        // Cek apakah user diblokir
        if ($user->is_blocked) {
            $this->setState($chatId, self::STATE_IDLE);
            $this->sendMessage(
                $chatId,
                "🚫 Akun dengan email <code>{$email}</code> <b>diblokir</b>.\n\n"
                    . "Hubungi admin untuk informasi lebih lanjut."
            );
            return;
        }

        // Cek apakah email sudah linked ke chat lain — tetap izinkan verifikasi
        $relinking = $user->telegram_id && $user->telegram_id !== $chatId;

        // Kirim kode verifikasi & lanjut ke step kode
        $this->sendVerificationCode($chatId, $user, $email, $relinking);
    }

    /**
     * Send verification code and move to STATE_WAITING_CODE.
     *
     * @param  string  $chatId
     * @param  User    $user
     * @param  string  $email
     * @param  bool    $relinking  true jika user sudah linked ke chat lain
     * @param  bool    $isNew      true jika user baru dibuat via Telegram
     */
    protected function sendVerificationCode(
        string $chatId,
        User $user,
        string $email,
        bool $relinking = false,
        bool $isNew = false,
    ): void {
        // Generate kode verifikasi 6 digit
        $code = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);

        // Simpan kode ke user
        $user->update([
            'telegram_link_code' => $code,
            'telegram_link_expires_at' => now()->addMinutes(10),
        ]);

        // Simpan email di cache untuk step berikutnya
        Cache::put("tg_link_{$chatId}_email", $email, now()->addMinutes(10));
        if ($relinking) {
            Cache::put("tg_link_{$chatId}_relinking", true, now()->addMinutes(10));
        }
        if ($isNew) {
            Cache::put("tg_link_{$chatId}_new_user", true, now()->addMinutes(10));
        }

        // Kirim kode ke email (queued)
        try {
            Mail::to($email)->queue(new TelegramVerificationCode($code, $user->name));
        } catch (\Exception $e) {
            Log::error('Failed to send telegram verification email', ['error' => $e->getMessage()]);
            $this->setState($chatId, self::STATE_IDLE);
            $this->sendMessage($chatId, "❌ Gagal mengirim email verifikasi. Coba lagi nanti.");
            return;
        }

        $this->setState($chatId, self::STATE_WAITING_CODE);

        if ($relinking) {
            $this->sendMessage(
                $chatId,
                "⚠️ Email ini sudah terhubung ke akun Telegram lain.\n\n"
                    . "📧 Kode verifikasi telah dikirim ke <code>{$email}</code>\n\n"
                    . "Masukkan 6 digit kode untuk <b>mengganti</b> koneksi Telegram ke akun ini:"
            );
        } elseif ($isNew) {
            $this->sendMessage(
                $chatId,
                "✅ Akun berhasil dibuat!\n\n"
                    . "📧 Kode verifikasi telah dikirim ke <code>{$email}</code>\n\n"
                    . "Masukkan 6 digit kode yang kamu terima:"
            );
        } else {
            $this->sendMessage(
                $chatId,
                "📧 Kode verifikasi telah dikirim ke <code>{$email}</code>\n\n"
                    . "Masukkan 6 digit kode yang kamu terima:"
            );
        }
    }

    /**
     * Handle verification code input.
     */
    protected function handleCodeInput(string $chatId, string $code): void
    {
        // Ambil email dari cache
        $email = Cache::get("tg_link_{$chatId}_email");

        if (!$email) {
            $this->setState($chatId, self::STATE_IDLE);
            $this->sendMessage($chatId, "⚠️ Sesi verifikasi telah kedaluwarsa.\nKirim /start untuk memulai ulang.");
            return;
        }

        $user = User::where('email', $email)->first();

        if (!$user || $user->telegram_link_code !== $code) {
            $this->sendMessage($chatId, "❌ Kode salah. Silakan coba lagi:");
            return;
        }

        // Cek blocked (jangan biarkan user yang diblokir selesaikan linking)
        if ($user->is_blocked) {
            $this->setState($chatId, self::STATE_IDLE);
            Cache::forget("tg_link_{$chatId}_email");
            Cache::forget("tg_link_{$chatId}_relinking");
            $this->sendMessage($chatId, "🚫 Akun ini <b>diblokir</b>. Hubungi admin.");
            return;
        }

        // Cek expired
        if ($user->telegram_link_expires_at && $user->telegram_link_expires_at->isPast()) {
            $this->setState($chatId, self::STATE_IDLE);
            Cache::forget("tg_link_{$chatId}_email");
            $this->sendMessage($chatId, "⚠️ Kode telah kedaluwarsa.\nKirim /start untuk memulai ulang.");
            return;
        }

        // Berhasil — simpan telegram_id, bersihkan kode
        $user->update([
            'telegram_id' => $chatId,
            'telegram_link_code' => null,
            'telegram_link_expires_at' => null,
        ]);

        $this->setState($chatId, self::STATE_IDLE);
        Cache::forget("tg_link_{$chatId}_email");
        Cache::forget("tg_link_{$chatId}_relinking");

        $isNew = Cache::pull("tg_link_{$chatId}_new_user");

        $this->sendMessage(
            $chatId,
            ($isNew ? "✅ Akun berhasil dibuat & terhubung!\n\n" : "✅ Akun berhasil terhubung!\n\n")
                . "👤 <b>{$user->name}</b>\n"
                . "📧 {$user->email}\n\n"
                . "Gunakan menu di bawah untuk navigasi.",
            $this->mainMenuKeyboard()
        );
    }

    /**
     * Handle order number input for tracking.
     */
    protected function handleOrderNumberInput(string $chatId, string $orderNumber, ?User $user): void
    {
        if (!$user) {
            $this->setState($chatId, self::STATE_IDLE);
            $this->sendMessage($chatId, "⚠️ Akun belum terhubung. Kirim /start");
            return;
        }

        $order = Order::where('order_number', $orderNumber)
            ->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->orWhere('customer_email', $user->email);
            })
            ->with('items')
            ->first();

        if (!$order) {
            $this->sendMessage($chatId, "❌ Pesanan <code>{$orderNumber}</code> tidak ditemukan.\n\nCoba lagi atau ketik /orders untuk melihat daftar pesanan:");
            return;
        }

        $this->setState($chatId, self::STATE_IDLE);
        $this->sendOrderDetail($chatId, $order);
    }

    /**
     * Handle menu text buttons.
     */
    protected function handleMenuText(string $chatId, string $text, User $user): void
    {
        switch ($text) {
            case '🛒 Belanja':
                $this->sendCategories($chatId);
                break;

            case '📦 Pesanan Saya':
                $this->sendOrdersList($chatId, $user);
                break;

            case '🔍 Lacak Pesanan':
                $this->setState($chatId, self::STATE_WAITING_ORDER_NUMBER);
                $this->sendMessage($chatId, "🔍 Kirim nomor pesanan kamu:\n(Contoh: <code>INV-A1B2C3D4</code>)");
                break;

            case '👤 Akun Saya':
                $this->sendAccountInfo($chatId, $user);
                break;

            case '❓ Bantuan':
                $this->sendHelp($chatId);
                break;

            default:
                $this->sendMessage($chatId, "❓ Pilih menu di bawah atau ketik /help untuk bantuan.", $this->mainMenuKeyboard());
                break;
        }
    }

    /**
     * Send orders list to user.
     */
    protected function sendOrdersList(string $chatId, User $user): void
    {
        $orders = Order::where(function ($q) use ($user) {
            $q->where('user_id', $user->id)
                ->orWhere('customer_email', $user->email);
        })
            ->with('items')
            ->latest()
            ->limit(10)
            ->get();

        if ($orders->isEmpty()) {
            $this->sendMessage($chatId, "📦 Kamu belum memiliki pesanan.\n\nKunjungi website kami untuk mulai belanja!", $this->mainMenuKeyboard());
            return;
        }

        $text = "📦 <b>Pesanan Terakhir</b>\n━━━━━━━━━━━━━━━━\n";

        foreach ($orders as $order) {
            $statusEmoji = match ($order->status) {
                'pending' => '⏳',
                'paid' => '💳',
                'completed' => '✅',
                'cancelled' => '❌',
                default => '❓',
            };
            $statusLabel = match ($order->status) {
                'pending' => 'Menunggu Bayar',
                'paid' => 'Dibayar',
                'completed' => 'Selesai',
                'cancelled' => 'Dibatalkan',
                default => $order->status,
            };

            $date = $order->created_at->format('d M Y');
            $total = 'Rp ' . number_format($order->total_amount, 0, ',', '.');
            $itemCount = $order->items->sum('quantity');

            $text .= "\n{$statusEmoji} <code>{$order->order_number}</code>\n"
                . "   📅 {$date} · {$itemCount} item\n"
                . "   💰 {$total} · <b>{$statusLabel}</b>\n";
        }

        $text .= "\n━━━━━━━━━━━━━━━━\nKetik nomor pesanan untuk melihat detail.";

        $this->setState($chatId, self::STATE_WAITING_ORDER_NUMBER);
        $this->sendMessage($chatId, $text, $this->mainMenuKeyboard());
    }

    /**
     * Send single order detail.
     */
    protected function sendOrderDetail(string $chatId, Order $order): void
    {
        $statusLabel = match ($order->status) {
            'pending' => '⏳ Menunggu Pembayaran',
            'paid' => '💳 Sudah Dibayar',
            'completed' => '✅ Selesai',
            'cancelled' => '❌ Dibatalkan',
            default => $order->status,
        };

        $items = $order->items->map(function ($item) {
            $qty = $item->quantity > 1 ? " x{$item->quantity}" : '';
            return "  • {$item->product_name} ({$item->variant_name}){$qty}\n    Rp " . number_format($item->subtotal, 0, ',', '.');
        })->implode("\n");

        $text = "📋 <b>Detail Pesanan</b>\n"
            . "━━━━━━━━━━━━━━━━\n"
            . "🔖 <code>{$order->order_number}</code>\n"
            . "📅 {$order->created_at->format('d M Y, H:i')}\n"
            . "━━━━━━━━━━━━━━━━\n"
            . "{$items}\n"
            . "━━━━━━━━━━━━━━━━\n"
            . "💰 Total: <b>Rp " . number_format($order->total_amount, 0, ',', '.') . "</b>\n"
            . "📌 Status: {$statusLabel}";

        if ($order->status === 'pending') {
            $text .= "\n\n🔗 <a href=\"" . url('/pesanan/' . $order->order_number) . "\">Bayar di Website</a>";
        }

        if ($order->status === 'cancelled' && $order->canceled_at) {
            $text .= "\n📅 Dibatalkan: {$order->canceled_at->format('d M Y, H:i')}";
        }

        $text .= "\n\n🔗 <a href=\"" . url('/pesanan/' . $order->order_number) . "\">Lihat di Website</a>";

        $this->sendMessage($chatId, $text, $this->mainMenuKeyboard());
    }

    /**
     * Send account info.
     */
    protected function sendAccountInfo(string $chatId, User $user): void
    {
        $orderCount = Order::where(function ($q) use ($user) {
            $q->where('user_id', $user->id)
                ->orWhere('customer_email', $user->email);
        })->count();

        $completedCount = Order::where(function ($q) use ($user) {
            $q->where('user_id', $user->id)
                ->orWhere('customer_email', $user->email);
        })->where('status', 'completed')->count();

        $text = "👤 <b>Akun Saya</b>\n"
            . "━━━━━━━━━━━━━━━━\n"
            . "🆔 ID: <code>{$user->id}</code>\n"
            . "👤 Nama: {$user->name}\n"
            . "📧 Email: {$user->email}\n"
            . "💬 Chat ID: <code>{$user->telegram_id}</code>\n"
            . "📅 Bergabung: {$user->created_at->format('d M Y')}\n"
            . "━━━━━━━━━━━━━━━━\n"
            . "📦 Total Pesanan: {$orderCount}\n"
            . "✅ Selesai: {$completedCount}\n"
            . "━━━━━━━━━━━━━━━━\n"
            . "🔗 <a href=\"" . url('/') . "\">Buka Website</a>";

        $this->sendMessage($chatId, $text, $this->mainMenuKeyboard());
    }

    /**
     * Unlink telegram account.
     */
    protected function unlinkAccount(string $chatId, User $user): void
    {
        $user->update(['telegram_id' => null]);
        $this->setState($chatId, self::STATE_IDLE);
        $this->sendMessage(
            $chatId,
            "✅ Akun Telegram berhasil diputus.\n\nKetik /start untuk menghubungkan kembali."
        );
    }

    /**
     * Send help message.
     */
    protected function sendHelp(string $chatId): void
    {
        $text = "❓ <b>Bantuan</b>\n"
            . "━━━━━━━━━━━━━━━━\n\n"
            . "📋 <b>Perintah:</b>\n"
            . "/start — Mulai & hubungkan akun\n"
            . "/shop — Lihat produk & belanja\n"
            . "/orders — Lihat pesanan\n"
            . "/account — Info akun\n"
            . "/unlink — Putuskan akun Telegram\n"
            . "/help — Tampilkan bantuan\n\n"
            . "🛒 <b>Cara Belanja:</b>\n"
            . "1. Ketuk <b>🛒 Belanja</b> di menu\n"
            . "2. Pilih kategori & produk\n"
            . "3. Pilih varian & konfirmasi\n"
            . "4. Bayar via QRIS\n\n"
            . "💡 Kamu juga bisa menggunakan tombol menu di bawah.";

        $this->sendMessage($chatId, $text, $this->mainMenuKeyboard());
    }

    // ─── Helpers ─────────────────────────────────────────

    /**
     * Send message with optional keyboard.
     */
    protected function sendMessage(string $chatId, string $text, ?array $replyMarkup = null): void
    {
        $payload = [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'HTML',
            'disable_web_page_preview' => true,
        ];

        if ($replyMarkup) {
            $payload['reply_markup'] = json_encode($replyMarkup);
        }

        try {
            Http::timeout(10)->post(
                "https://api.telegram.org/bot{$this->botToken}/sendMessage",
                $payload
            );
        } catch (\Exception $e) {
            Log::error('TelegramBot sendMessage failed', [
                'chat_id' => $chatId,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Edit an existing message (for inline keyboard pagination).
     */
    protected function editMessage(string $chatId, int $messageId, string $text, ?array $replyMarkup = null): void
    {
        $payload = [
            'chat_id' => $chatId,
            'message_id' => $messageId,
            'text' => $text,
            'parse_mode' => 'HTML',
            'disable_web_page_preview' => true,
        ];

        if ($replyMarkup) {
            $payload['reply_markup'] = json_encode($replyMarkup);
        }

        try {
            Http::timeout(10)->post(
                "https://api.telegram.org/bot{$this->botToken}/editMessageText",
                $payload
            );
        } catch (\Exception $e) {
            Log::error('TelegramBot editMessage failed', [
                'chat_id' => $chatId,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Answer a callback query to dismiss the loading indicator.
     */
    protected function answerCallbackQuery(string $callbackQueryId, ?string $text = null): void
    {
        $payload = ['callback_query_id' => $callbackQueryId];
        if ($text) {
            $payload['text'] = $text;
            $payload['show_alert'] = true;
        }

        try {
            Http::timeout(5)->post(
                "https://api.telegram.org/bot{$this->botToken}/answerCallbackQuery",
                $payload
            );
        } catch (\Exception $e) {
            Log::error('TelegramBot answerCallbackQuery failed', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Delete a message.
     */
    protected function deleteMessage(string $chatId, int $messageId): void
    {
        try {
            Http::timeout(5)->post(
                "https://api.telegram.org/bot{$this->botToken}/deleteMessage",
                [
                    'chat_id' => $chatId,
                    'message_id' => $messageId,
                ]
            );
        } catch (\Exception $e) {
            Log::error('TelegramBot deleteMessage failed', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Format currency to Rupiah.
     */
    protected function formatRupiah(float|int $amount): string
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }

    // ─── Shopping Flow ───────────────────────────────────

    /**
     * Handle inline keyboard callback queries.
     */
    protected function handleCallbackQuery(array $callbackQuery): void
    {
        $chatId = (string) $callbackQuery['message']['chat']['id'];
        $messageId = (int) $callbackQuery['message']['message_id'];
        $data = $callbackQuery['data'] ?? '';
        $callbackId = $callbackQuery['id'];

        // Pastikan user sudah linked
        $user = User::where('telegram_id', $chatId)->first();
        if (!$user) {
            $this->answerCallbackQuery($callbackId, '⚠️ Kamu belum menghubungkan akun. Ketik /start');
            return;
        }

        $parts = explode(':', $data);
        $action = $parts[0] ?? '';

        match ($action) {
            'cat'       => $this->handleCategoryCallback($chatId, $messageId, $callbackId, $parts),
            'detail'    => $this->handleDetailCallback($chatId, $messageId, $callbackId, $parts),
            'buy'       => $this->handleBuyCallback($chatId, $messageId, $callbackId, $parts, $user),
            'confirm'   => $this->handleConfirmCallback($chatId, $messageId, $callbackId, $parts, $user),
            'cancel'    => $this->handleCancelCallback($chatId, $messageId, $callbackId),
            'nostock'   => $this->answerCallbackQuery($callbackId, '❌ Varian ini sedang habis.'),
            default     => $this->answerCallbackQuery($callbackId),
        };
    }

    /**
     * Show categories list with inline buttons.
     */
    protected function sendCategories(string $chatId): void
    {
        $categories = Category::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->withCount(['products' => function ($q) {
                $q->where('is_active', true);
            }])
            ->get();

        if ($categories->isEmpty()) {
            $this->sendMessage($chatId, "🛒 Belum ada kategori tersedia.", $this->mainMenuKeyboard());
            return;
        }

        $text = "🛒 <b>Kategori Produk</b>\n━━━━━━━━━━━━━━━━\nPilih kategori yang ingin kamu lihat:";

        $buttons = [];
        foreach ($categories as $cat) {
            $count = $cat->products_count;
            $buttons[] = [
                ['text' => "📂 {$cat->name} ({$count})", 'callback_data' => "cat:{$cat->id}"],
            ];
        }

        $this->sendMessage($chatId, $text, [
            'inline_keyboard' => $buttons,
        ]);
    }

    /**
     * Handle category callback → show products in category (paginated).
     */
    protected function handleCategoryCallback(string $chatId, int $messageId, string $callbackId, array $parts): void
    {
        // Handle "Back to Categories" button
        if (($parts[1] ?? '') === 'back') {
            $this->answerCallbackQuery($callbackId);
            // Delete old inline message, then send fresh categories
            $this->deleteMessage($chatId, $messageId);
            $this->sendCategories($chatId);
            return;
        }

        $categoryId = (int) ($parts[1] ?? 0);
        $page = (int) ($parts[2] ?? 1);
        $perPage = 5;

        $category = Category::find($categoryId);
        if (!$category || !$category->is_active) {
            $this->answerCallbackQuery($callbackId, '❌ Kategori tidak ditemukan.');
            return;
        }

        $query = Product::where('category_id', $categoryId)
            ->where('is_active', true)
            ->with(['variants' => function ($q) {
                $q->where('is_active', true)->orderBy('sort_order')->orderBy('price');
            }])
            ->orderBy('sort_order')
            ->orderBy('name');

        $total = $query->count();
        $totalPages = max(1, (int) ceil($total / $perPage));
        $page = max(1, min($page, $totalPages));

        $products = $query->skip(($page - 1) * $perPage)->take($perPage)->get();

        $text = "📂 <b>{$category->name}</b>\n";
        if ($category->description) {
            $text .= "{$category->description}\n";
        }
        $text .= "━━━━━━━━━━━━━━━━\n";
        $text .= "Hal {$page}/{$totalPages} · {$total} produk\n\n";

        if ($products->isEmpty()) {
            $text .= "Belum ada produk di kategori ini.";
        }

        $buttons = [];
        foreach ($products as $prod) {
            $minPrice = $prod->variants->min('effective_price');
            $priceStr = $minPrice !== null ? $this->formatRupiah($minPrice) : '-';
            $buttons[] = [
                ['text' => "🏷️ {$prod->name} · {$priceStr}", 'callback_data' => "detail:{$prod->id}:{$categoryId}:{$page}"],
            ];
        }

        // Pagination row
        $navRow = [];
        if ($page > 1) {
            $navRow[] = ['text' => '⬅️ Sebelumnya', 'callback_data' => "cat:{$categoryId}:" . ($page - 1)];
        }
        if ($page < $totalPages) {
            $navRow[] = ['text' => 'Selanjutnya ➡️', 'callback_data' => "cat:{$categoryId}:" . ($page + 1)];
        }
        if (!empty($navRow)) {
            $buttons[] = $navRow;
        }

        // Back button
        $buttons[] = [['text' => '🔙 Kembali ke Kategori', 'callback_data' => 'cat:back']];

        $this->editMessage($chatId, $messageId, $text, ['inline_keyboard' => $buttons]);
        $this->answerCallbackQuery($callbackId);
    }

    /**
     * Handle "cat:back" → re-show categories.
     */
    /**
     * Handle product detail callback → show product info + variant buttons.
     */
    protected function handleDetailCallback(string $chatId, int $messageId, string $callbackId, array $parts): void
    {
        $productId = (int) ($parts[1] ?? 0);
        $categoryId = (int) ($parts[2] ?? 0);
        $backPage = (int) ($parts[3] ?? 1);

        $product = Product::with(['variants' => function ($q) {
            $q->where('is_active', true)->orderBy('sort_order')->orderBy('price');
        }])->find($productId);

        if (!$product || !$product->is_active) {
            $this->answerCallbackQuery($callbackId, '❌ Produk tidak ditemukan.');
            return;
        }

        $text = "🏷️ <b>{$product->name}</b>\n";
        $text .= "━━━━━━━━━━━━━━━━\n";
        if ($product->description) {
            $text .= "{$product->description}\n\n";
        }

        $buttons = [];
        foreach ($product->variants as $variant) {
            $price = $this->formatRupiah($variant->effective_price);

            if ($variant->is_discounted) {
                $oldPrice = $this->formatRupiah($variant->price);
                $label = "💰 {$variant->name} · ~{$oldPrice}~ {$price}";
            } else {
                $label = "💰 {$variant->name} · {$price}";
            }

            $buttons[] = [
                ['text' => $label, 'callback_data' => "buy:{$variant->id}:{$categoryId}:{$backPage}"],
            ];
        }

        // Back button
        $buttons[] = [['text' => '🔙 Kembali', 'callback_data' => "cat:{$categoryId}:{$backPage}"]];

        $this->editMessage($chatId, $messageId, $text, ['inline_keyboard' => $buttons]);
        $this->answerCallbackQuery($callbackId);
    }

    /**
     * Handle buy callback → show checkout confirmation.
     */
    protected function handleBuyCallback(string $chatId, int $messageId, string $callbackId, array $parts, User $user): void
    {
        $variantId = (int) ($parts[1] ?? 0);
        $categoryId = (int) ($parts[2] ?? 0);
        $backPage = (int) ($parts[3] ?? 1);

        $variant = ProductVariant::with('product')->find($variantId);
        if (!$variant || !$variant->is_active || !$variant->product || !$variant->product->is_active) {
            $this->answerCallbackQuery($callbackId, '❌ Produk tidak tersedia.');
            return;
        }

        $product = $variant->product;
        $price = $this->formatRupiah($variant->effective_price);

        $text = "🛒 <b>Konfirmasi Pembelian</b>\n"
            . "━━━━━━━━━━━━━━━━\n"
            . "📦 Produk: <b>{$product->name}</b>\n"
            . "🏷️ Varian: {$variant->name}\n"
            . "💰 Harga: <b>{$price}</b>\n"
            . "━━━━━━━━━━━━━━━━\n\n"
            . "👤 Atas nama: <b>{$user->name}</b>\n"
            . "📧 Email: {$user->email}\n\n"
            . "Konfirmasi pembelian?";

        $buttons = [
            [
                ['text' => '✅ Beli Sekarang', 'callback_data' => "confirm:{$variant->id}"],
            ],
            [
                ['text' => '❌ Batal', 'callback_data' => "detail:{$product->id}:{$categoryId}:{$backPage}"],
            ],
        ];

        $this->editMessage($chatId, $messageId, $text, ['inline_keyboard' => $buttons]);
        $this->answerCallbackQuery($callbackId);
    }

    /**
     * Handle confirm callback → create order and send payment link.
     */
    protected function handleConfirmCallback(string $chatId, int $messageId, string $callbackId, array $parts, User $user): void
    {
        $variantId = (int) ($parts[1] ?? 0);

        $variant = ProductVariant::with('product')->find($variantId);
        if (!$variant || !$variant->is_active || !$variant->product || !$variant->product->is_active) {
            $this->answerCallbackQuery($callbackId, '❌ Produk tidak tersedia.');
            return;
        }

        $product = $variant->product;
        $amount = (float) $variant->effective_price;

        try {
            $order = DB::transaction(function () use ($user, $product, $variant, $amount) {
                $order = Order::create([
                    'user_id'         => $user->id,
                    'order_number'    => Order::generateOrderNumber(),
                    'customer_name'   => $user->name,
                    'customer_email'  => $user->email,
                    'customer_phone'  => '',
                    'total_amount'    => $amount,
                    'status'          => 'pending',
                    'payment_method'  => 'qris',
                    'notes'           => 'Dibuat via Telegram Bot',
                ]);

                OrderItem::create([
                    'order_id'      => $order->id,
                    'product_id'    => $product->id,
                    'variant_id'    => $variant->id,
                    'product_name'  => $product->name,
                    'variant_name'  => $variant->name,
                    'price'         => $amount,
                    'quantity'      => 1,
                    'subtotal'      => $amount,
                ]);

                return $order;
            });

            // Generate payment link via Pakasir
            $pakasir = app(PakasirService::class);
            $amountInt = (int) round($amount);
            $payment = $pakasir->createQrisTransaction($order->order_number, $amountInt);

            if ($payment) {
                $order->update([
                    'payment_ref'  => $payment['payment_number'] ?? null,
                    'payment_url'  => $pakasir->getPaymentUrl($order->order_number, $amountInt),
                ]);
            } else {
                // Fallback: generate direct URL
                $order->update([
                    'payment_url' => $pakasir->getPaymentUrl($order->order_number, $amountInt),
                ]);
            }

            $price = $this->formatRupiah($amount);
            $text = "✅ <b>Pesanan Berhasil Dibuat!</b>\n"
                . "━━━━━━━━━━━━━━━━\n"
                . "🔖 Nomor: <code>{$order->order_number}</code>\n"
                . "📦 {$product->name} ({$variant->name})\n"
                . "💰 Total: <b>{$price}</b>\n"
                . "━━━━━━━━━━━━━━━━\n\n"
                . "⏰ Pesanan akan kedaluwarsa dalam 1 jam.\n"
                . "🔗 <a href=\"" . url('/pesanan/' . $order->order_number) . "\">Lihat Pesanan & Bayar</a>\n\n"
                . "Gunakan menu di bawah untuk navigasi.";

            $this->editMessage($chatId, $messageId, $text);
            $this->answerCallbackQuery($callbackId, '✅ Pesanan berhasil dibuat!');

            // Kirim email notifikasi (queued)
            try {
                Mail::to($user->email)->queue(new \App\Mail\OrderPendingPayment($order));
            } catch (\Exception $e) {
                Log::error('Failed to send order email', ['error' => $e->getMessage()]);
            }
        } catch (\Exception $e) {
            Log::error('TelegramBot order creation failed', ['error' => $e->getMessage()]);
            $this->answerCallbackQuery($callbackId, '❌ Gagal membuat pesanan. Coba lagi.');
        }
    }

    /**
     * Handle cancel callback → back to categories.
     */
    protected function handleCancelCallback(string $chatId, int $messageId, string $callbackId): void
    {
        $this->editMessage($chatId, $messageId, "🛒 Pembelian dibatalkan.\n\nPilih menu di bawah untuk navigasi.");
        $this->answerCallbackQuery($callbackId);
    }

    /**
     * Main menu ReplyKeyboardMarkup.
     */
    protected function mainMenuKeyboard(): array
    {
        return [
            'keyboard' => [
                [['text' => '🛒 Belanja']],
                [['text' => '📦 Pesanan Saya'], ['text' => '🔍 Lacak Pesanan']],
                [['text' => '👤 Akun Saya'], ['text' => '❓ Bantuan']],
            ],
            'resize_keyboard' => true,
            'one_time_keyboard' => false,
        ];
    }

    /**
     * Get user conversation state from cache.
     */
    protected function getState(string $chatId): string
    {
        return Cache::get("tg_state_{$chatId}", self::STATE_IDLE);
    }

    /**
     * Set user conversation state in cache.
     */
    protected function setState(string $chatId, string $state): void
    {
        Cache::put("tg_state_{$chatId}", $state, now()->addMinutes(30));
    }
}
