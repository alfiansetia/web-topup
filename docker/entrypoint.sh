#!/bin/sh
set -e

# Storage symlink (runtime, karena volume mount menimpa build-time symlink)
if [ ! -L public/storage ]; then
    php artisan storage:link --force 2>/dev/null || true
fi

# Pastikan permission benar
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true
chmod -R 775 storage bootstrap/cache 2>/dev/null || true

# Jalankan CMD yang diberikan
exec "$@"
