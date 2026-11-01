#!/bin/sh
set -e

echo "Starting Laravel..."

echo "Clearing config cache..."
php artisan config:clear

echo "Running migrations..."
php artisan migrate --force -vvv

echo "Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Starting queue worker in background..."
php artisan queue:work redis --tries=3 --backoff=10 --sleep=3 &

echo "Starting server..."
exec php artisan serve \
    --host=0.0.0.0 \
    --port=${PORT:-10000}
