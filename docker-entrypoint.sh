#!/bin/sh

# Run database migrations
php artisan migrate --force

# Start PHP-FPM
php-fpm &

# Start Nginx
nginx -g 'daemon off;'