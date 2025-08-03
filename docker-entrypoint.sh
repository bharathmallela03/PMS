#!/bin/sh

# Exit immediately if a command exits with a non-zero status.
set -e

# Change to the application directory
cd /var/www/html

# Clear any cached configurations
echo "Clearing caches..."
php artisan optimize:clear

# Run database migrations
echo "Running database migrations..."
php artisan migrate --force

# Run database seeders (if you need them on every start)
echo "Running database seeders..."
php artisan db:seed --force

# IMPORTANT: Create the storage symlink to allow file uploads
echo "Linking storage directory..."
php artisan storage:link

# IMPORTANT: Set correct permissions for storage and cache
echo "Setting storage permissions..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Start PHP-FPM in the background
php-fpm &

# Start Nginx in the foreground
echo "Starting Nginx..."
nginx -g 'daemon off;'