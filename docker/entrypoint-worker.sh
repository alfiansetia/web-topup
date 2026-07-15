#!/bin/sh
set -e

# ── Tunggu MySQL siap ──
echo "⏳ Menunggu MySQL..."
max_retries=30
counter=0
until php artisan db:monitor > /dev/null 2>&1; do
    counter=$((counter + 1))
    if [ "$counter" -ge "$max_retries" ]; then
        echo "❌ MySQL tidak merespon setelah $max_retries percobaan."
        exit 1
    fi
    sleep 2
done
echo "✅ MySQL terhubung."

# Permission
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true
chmod -R 775 storage bootstrap/cache 2>/dev/null || true

echo "🚀 Worker siap."
exec "$@"
