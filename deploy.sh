#!/bin/bash
set -e

cd /var/www/ofm-vsl-saas

git pull origin main

composer install --no-dev --optimize-autoloader --no-interaction

php artisan migrate --force

php artisan config:cache
php artisan route:cache
php artisan view:cache

php artisan storage:link 2>/dev/null || true

sudo systemctl reload php8.3-fpm
sudo systemctl reload nginx

echo "Deploy done."
