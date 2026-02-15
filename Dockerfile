# Stage 1: Build frontend assets
FROM node:20-alpine AS frontend
WORKDIR /app
COPY package.json package-lock.json* ./
RUN npm install
COPY vite.config.js ./
COPY resources/ ./resources/
RUN npm run build

# Stage 2: Install PHP dependencies
FROM composer:2 AS composer
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction

# Stage 3: Final image
FROM php:8.2-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    curl \
    && docker-php-ext-install pdo pdo_mysql zip bcmath \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

# Copy application code
COPY . .

# Copy composer dependencies from build stage
COPY --from=composer /app/vendor ./vendor

# Copy built frontend assets from build stage
COPY --from=frontend /app/public/build ./public/build

# Set permissions (storage + cache must be writable)
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# Expose port (platforms usually provide PORT env variable)
EXPOSE 10000

# Start the application (note: set APP_KEY/DB_* via platform env vars)
CMD php artisan serve --host=0.0.0.0 --port=${PORT:-10000}
