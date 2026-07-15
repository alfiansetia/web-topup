# AA Store — Top Up Game & Layanan Digital

Platform top up game dan layanan digital berbasis web dengan pembayaran QRIS (Pakasir) dan notifikasi Telegram.

## Stack Teknologi

| Layer             | Teknologi                                   |
| ----------------- | ------------------------------------------- |
| **Backend**       | Laravel 13, PHP 8.3                         |
| **Frontend**      | Vue 3, Inertia.js v2, Tailwind CSS v4       |
| **Build**         | Vite 8                                      |
| **Database**      | MySQL / SQLite                              |
| **Auth**          | Laravel Sanctum, Google OAuth (Socialite)   |
| **Payment**       | Pakasir (QRIS)                              |
| **Notifications** | Telegram Bot API, SMTP (Mailtrap / Mailgun) |
| **Queue**         | Database driver                             |

## Fitur

### Toko (Public)

- Katalog produk berdasarkan kategori
- Detail produk dengan varian & harga
- Checkout dengan pembayaran QRIS
- Lacak pesanan tanpa login (via nomor pesanan)
- Autentikasi email/password & Google OAuth

### Dashboard User

- Riwayat pesanan
- Profil & pengaturan password
- Set password untuk user yang daftar via Telegram/Google

### Admin Panel

- Dashboard statistik (revenue, pesanan, produk)
- Kelola kategori, produk, varian & stok (akun/item)
- Kelola pesanan (verifikasi pembayaran, assign akun, selesaikan, batalkan)
- Buat pesanan manual
- Kelola pengguna (CRUD, blokir, hubungkan Telegram Chat ID)
- Kirim ulang email notifikasi

### Telegram Bot (`@aa1_store_bot`)

- Menu interaktif: Belanja, Pesanan Saya, Lacak Pesanan, Akun, Bantuan
- Shopping flow: Kategori → Produk → Varian → Checkout → Pembayaran
- Pembuatan pesanan langsung dari bot
- Notifikasi status pesanan (dibayar, selesai, dibatalkan)
- Perintah: `/start`, `/help`, `/shop`, `/orders`, `/account`, `/unlink`

### Notifikasi

- **Email** (queued): Pending payment, Pembayaran berhasil, Pesanan selesai, Dibatalkan
- **Telegram ke user**: Status pesanan berubah
- **Telegram ke admin**: Pesanan baru, Pembayaran diterima

### Otomatisasi

- Auto-cancel pesanan pending > 1 jam (scheduler `orders:cancel-expired`)
- Webhook Pakasir untuk verifikasi pembayaran otomatis

## Struktur Proyek

```
app/
├── Commands/                  # Artisan commands
│   └── CancelExpiredOrders.php
├── Http/
│   ├── Controllers/
│   │   ├── Admin/             # Admin panel controllers
│   │   ├── Auth/              # Login, register, Google OAuth
│   │   ├── DashboardController.php
│   │   ├── ShopController.php
│   │   ├── PakasirWebhookController.php
│   │   └── TelegramBotController.php
│   └── Middleware/
├── Mail/                      # Mailable classes (queued)
├── Models/                    # Eloquent models
├── Services/
│   ├── PakasirService.php     # Payment gateway integration
│   ├── TelegramBotService.php # User-facing bot logic
│   └── TelegramService.php    # Admin notification service
resources/
└── views/emails/              # Blade email templates
routes/
├── admin.php                  # Admin routes
├── web.php                    # Public & dashboard routes
└── console.php                # Scheduler
```

## Instalasi

### Prerequisites

- PHP >= 8.3
- Composer
- Node.js >= 18 & npm
- MySQL atau SQLite

### Langkah

```bash
# 1. Clone & install dependencies
git clone <repo-url> web-topup
cd web-topup
composer install
npm install

# 2. Environment
cp .env.example .env
php artisan key:generate

# 3. Konfigurasi .env (lihat bagian Environment di bawah)

# 4. Database
php artisan migrate --seed

# 5. Storage symlink
php artisan storage:link

# 6. Build frontend
npm run build

# 7. Jalankan
php artisan serve
```

### Queue Worker (wajib untuk email)

```bash
php artisan queue:work
```

### Scheduler (wajib untuk auto-cancel)

Tambahkan ke cron atau jalankan:

```bash
php artisan schedule:run
```

### Docker (alternatif)

Jalankan seluruh stack (Nginx, PHP-FPM, MySQL, Queue Worker, Scheduler) dengan Docker:

```bash
# 1. Copy environment
cp .env.example .env

# 2. Edit .env — set DB_CONNECTION=mysql, sesuaikan APP_URL, credentials, dll.

# 3. Build & start container
docker compose up -d --build

# 4. Install dependencies di dalam container
docker compose exec app composer install --no-dev
docker compose exec app php artisan key:generate

# 5. Migrate & seed database
docker compose exec app php artisan migrate --seed
```

> **Note:** `storage:link` dijalankan otomatis via entrypoint saat container start.

Aplikasi berjalan di **http://localhost:8999**

| Service       | Container         | Fungsi                             |
| ------------- | ----------------- | ---------------------------------- |
| **Nginx**     | `topup-nginx`     | Web server (port 8999 → 80)        |
| **PHP-FPM**   | `topup-app`       | Aplikasi Laravel                   |
| **Queue**     | `topup-queue`     | Background job (email, notifikasi) |
| **Scheduler** | `topup-scheduler` | Cron setiap 60 detik               |
| **MySQL**     | `topup-mysql`     | Database (port 3307 → 3306)        |

Perintah berguna:

```bash
# Masuk ke container app
docker compose exec app bash

# Jalankan artisan command
docker compose exec app php artisan tinker

# Lihat log
docker compose logs -f app

# Restart semua container
docker compose restart

# Stop & hapus container
docker compose down

# Stop & hapus container + volume database
docker compose down -v
```

## Environment Variables

```env
# ── Aplikasi ──
APP_NAME="AA Store"
APP_URL=https://your-domain.com
APP_CS_WA=628xxxxxxxxxx        # Nomor WhatsApp CS
APP_CS_EMAIL=info@domain.com

# ── Database ──
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=web_topup
DB_USERNAME=root
DB_PASSWORD=

# ── Session ──
SESSION_DRIVER=database
SESSION_LIFETIME=120

# ── Queue ──
QUEUE_CONNECTION=database

# ── Payment Gateway (Pakasir) ──
PAKASIR_SLUG=your-slug
PAKASIR_SECRET_KEY=your-secret-key
PAKASIR_IS_PRODUCTION=false

# ── Telegram ──
TELEGRAM_BOT_TOKEN=your-bot-token
TELEGRAM_BOT_USERNAME=@your_bot
TELEGRAM_CHAT_IDS=chatid1,chatid2  # Admin chat IDs (comma separated)
TELEGRAM_NOTIFY_NEW_ORDER=true
TELEGRAM_NOTIFY_PAID=true

# ── Mail ──
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
MAIL_FROM_ADDRESS=noreply@your-domain.com
MAIL_FROM_NAME="${APP_NAME}"

# ── Google OAuth (opsional) ──
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_REDIRECT_URL=${APP_URL}/auth/google/callback
```

## Setup Telegram Bot

1. Buat bot via [@BotFather](https://t.me/BotFather), simpan token ke `TELEGRAM_BOT_TOKEN`
2. Set webhook:
    ```bash
    curl "https://api.telegram.org/bot<TOKEN>/setWebhook?url=<APP_URL>/telegram/webhook"
    ```
3. User menghubungkan akun: kirim `/start` ke bot, lalu masukkan email yang terdaftar
4. Admin mendapat notifikasi otomatis di chat ID yang terdaftar di `TELEGRAM_CHAT_IDS`

## Credential Default (Seed)

| Role  | Email           | Password   |
| ----- | --------------- | ---------- |
| Admin | admin@gmail.com | admin12345 |

## Perintah Artisan

```bash
# Batalkan pesanan pending yang expired (> 1 jam)
php artisan orders:cancel-expired

# Jalankan scheduler manual
php artisan schedule:run

# Reset & seed database
php artisan migrate:fresh --seed

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

## License

MIT
