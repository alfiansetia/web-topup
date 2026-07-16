#!/bin/sh
set -e

# ── Tunggu MySQL siap ──
echo "⏳ Menunggu MySQL di ${DB_HOST:-mysql}:${DB_PORT:-3306}..."
max_retries=30
counter=0
until php -r "new PDO('mysql:host=${DB_HOST:-mysql};port=${DB_PORT:-3306};dbname=${DB_DATABASE:-web_topup}', '${DB_USERNAME:-root}', '${DB_PASSWORD:-}');" > /dev/null 2>&1; do
    counter=$((counter + 1))
    if [ "$counter" -ge "$max_retries" ]; then
        echo "❌ MySQL tidak merespon setelah $max_retries percobaan."
        exit 1
    fi
    echo "  ↳ MySQL belum siap... ($counter/$max_retries)"
    sleep 2
done
echo "✅ MySQL terhubung."

# ── Migrate database (idempotent, aman dijalankan berulang) ──
echo "🔄 Menjalankan migrate..."
php artisan migrate --force --no-interaction

# ── Seed database (hanya sekali saat fresh install) ──
SEED_FLAG=storage/.docker_seeded
if [ ! -f "$SEED_FLAG" ]; then
    echo "🌱 Menjalankan database seeder..."
    php artisan db:seed --force --no-interaction && touch "$SEED_FLAG"
fi

# ── Storage symlink ──
if [ ! -L public/storage ]; then
    echo "🔗 Membuat storage link..."
    php artisan storage:link --force 2>/dev/null || true
fi

# ── Permission ──
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true
chmod -R 775 storage bootstrap/cache 2>/dev/null || true

echo "🚀 Container siap."

# Jalankan CMD yang diberikan
exec "$@"
