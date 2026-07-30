#!/bin/sh
set -e

# Update Nginx port if PORT environment variable is set by Railway
if [ -n "$PORT" ]; then
    sed -i "s/listen 8080;/listen ${PORT};/g" /etc/nginx/nginx.conf
    sed -i "s/listen \[::\]:8080;/listen \[::\]:${PORT};/g" /etc/nginx/nginx.conf
fi

# Ensure SQLite database exists if using SQLite
if [ "$DB_CONNECTION" = "sqlite" ] || [ -z "$DB_CONNECTION" ]; then
    mkdir -p /app/database
    touch /app/database/database.sqlite
    chmod 777 /app/database/database.sqlite
fi

# Ensure storage & cache directory permissions
mkdir -p /app/storage/app/public /app/storage/framework/cache /app/storage/framework/sessions /app/storage/framework/views /app/bootstrap/cache
chmod -R 777 /app/storage /app/bootstrap/cache

# Create storage symlink for uploaded media and avatars
php artisan storage:link --force || true

# Run database migrations
php artisan migrate --force || true

# Run Laravel & Filament optimizations for production panel caching
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true
php artisan filament:optimize || true

# Start process manager
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
