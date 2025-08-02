#!/bin/sh

# Exit immediately if a command exits with a non-zero status.
set -e

# Run database migrations
echo "Running database migrations..."
php artisan migrate --force

# Run database seeders
echo "Running database seeders..."
php artisan db:seed --force

# Start PHP-FPM in the background
php-fpm &

# Start Nginx in the foreground
echo "Starting Nginx..."
nginx -g 'daemon off;'
