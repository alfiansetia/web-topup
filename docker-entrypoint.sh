#!/bin/sh
set -e

echo "=== Menjalankan Tugas Inisialisasi Production ==="

# ==================== TAMBAHAN: TUNGGU SAMPAI DB DIBUAT ====================
echo "Memeriksa koneksi ke database ${DB_DATABASE}..."
php -r '
$count = 0;
while ($count < 30) { # Mencoba selama 60 detik maksimal
    try {
        // Mencoba connect langsung ke database spesifik aplikasi Anda
        $pdo = new PDO("mysql:host=" . getenv("DB_HOST") . ";port=3306;dbname=" . getenv("DB_DATABASE"), getenv("DB_USERNAME"), getenv("DB_PASSWORD"));
        exit(0);
    } catch (PDOException $e) {
        echo "Database belum siap atau belum dibuat, mencoba lagi dalam 2 detik...\n";
        sleep(2);
        $count++;
    }
}
echo "Gagal terhubung ke database setelah 1 menit. Menghentikan container.\n";
exit(1);
'
# ===========================================================================

# 1. Pastikan folder public/storage terhubung ke storage/app/public
if [ ! -d "/var/www/html/public/storage" ]; then
    echo "Membuat symlink storage..."
    php artisan storage:link --force || true
fi

# 2. Jalankan migrasi database otomatis secara aman dan paksa
echo "Menjalankan migrasi database..."
php artisan migrate --force

# 3. Hapus cache lama dan buat cache baru untuk performa maksimal
echo "Mengoptimalkan cache Laravel..."
php artisan optimize

echo "=== Aplikasi Siap Digunakan! ==="

# 4. Meneruskan perintah utama dari Dockerfile (yaitu: php-fpm -F)
exec "$@"