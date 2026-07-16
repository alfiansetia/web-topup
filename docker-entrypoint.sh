#!/bin/sh

set -e

echo "Waiting MySQL..."

until mysql \
    --skip-ssl \
    -h"$DB_HOST" \
    -u"$DB_USERNAME" \
    -p"$DB_PASSWORD" \
    -e "SELECT 1;" >/dev/null 2>&1
do
    echo "MySQL not ready..."
    sleep 2
done

echo "MySQL Ready"

composer install --no-dev --optimize-autoloader

npm install

npm run build

php artisan key:generate --force || true

php artisan storage:link || true

php artisan migrate --force

php artisan optimize

exec "$@"