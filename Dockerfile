# Stage 1: Build front-end assets
FROM node:18 as node_assets

WORKDIR /app
COPY package.json package-lock.json ./
RUN npm install
COPY . .
RUN npm run production

# Stage 2: Setup the PHP environment
FROM php:8.2-fpm

WORKDIR /var/www/html

# Install system dependencies for Laravel & Nginx
# --- FIX: Add libpq-dev for the PostgreSQL driver ---
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nginx \
    libzip-dev \
    libgd-dev \
    libpq-dev

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions required by the project
# --- FIX: Add pdo_pgsql for PostgreSQL connection ---
RUN docker-php-ext-install pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy application source code (excluding node_modules)
COPY . .

# Copy built assets from the node_assets stage
COPY --from=node_assets /app/public /var/www/html/public

# Copy Nginx config files
RUN rm -f /etc/nginx/sites-enabled/default
COPY nginx.conf /etc/nginx/sites-enabled/default
RUN rm /etc/nginx/nginx.conf
COPY nginx-main.conf /etc/nginx/nginx.conf

# Install Composer dependencies
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# Set correct permissions for storage and bootstrap cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Expose port 80 for Nginx
EXPOSE 80

# Entrypoint script to run migrations and start services
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
