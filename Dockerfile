# Multi-stage Dockerfile for BeyondTrails Laravel & Filament Panel on Railway
FROM php:8.4-fpm-alpine

# Set environment variables for Composer
ENV COMPOSER_ALLOW_SUPERUSER=1

# Install system dependencies, PHP extension build tools, Node.js & NPM
RUN apk add --no-cache \
    nginx \
    supervisor \
    curl \
    git \
    unzip \
    nodejs \
    npm \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    icu-dev \
    libsodium-dev \
    oniguruma-dev \
    sqlite-dev

# Configure and install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install -j$(nproc) \
    pdo \
    pdo_mysql \
    pdo_sqlite \
    sodium \
    opcache \
    gd \
    zip \
    intl \
    mbstring \
    bcmath

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Copy application files
COPY . /app

# Set correct storage & cache permissions
RUN mkdir -p /app/storage /app/bootstrap/cache /app/database /app/public/storage \
 && chmod -R 777 /app/storage /app/bootstrap/cache /app/database /app/public

# Install Node dependencies and compile production frontend assets
RUN npm install --no-audit --no-fund \
 && npm run build || true

# Install Composer dependencies (ignoring platform reqs for smooth Docker build)
RUN composer install --no-dev --optimize-autoloader --no-interaction --ignore-platform-reqs

# Copy Docker configuration files
COPY docker/opcache.ini /usr/local/etc/php/conf.d/opcache.ini
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# Expose port (Railway dynamically injects $PORT)
EXPOSE 8080

ENTRYPOINT ["/entrypoint.sh"]
