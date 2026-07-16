#!/bin/sh
set -e

echo "=== Menjalankan Tugas Inisialisasi Production ==="

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