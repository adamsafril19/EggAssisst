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
    libsqlite3-dev \
    libzip-dev \
    zip \
    unzip \
    curl \
    && docker-php-ext-install pdo pdo_sqlite zip bcmath \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

# Copy application code
COPY . .

# Copy composer dependencies from build stage
COPY --from=composer /app/vendor ./vendor

# Copy built frontend assets from build stage
COPY --from=frontend /app/public/build ./public/build

# Create SQLite database file
RUN mkdir -p database && touch database/database.sqlite

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache database

# Copy .env.example to .env if not exists
RUN cp .env.example .env

# Generate app key, cache config, and run migrations
RUN php artisan key:generate --force \
    && php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache \
    && php artisan migrate --force

# Expose port (Render uses PORT env variable)
EXPOSE 10000

# Start the application
CMD php artisan serve --host=0.0.0.0 --port=${PORT:-10000}
