#!/bin/sh
set -e

# Ensure required log and runtime directories exist
mkdir -p /var/log/supervisor /var/log/nginx /var/run/nginx /run/nginx

# Remove Alpine default Nginx configurations that interfere with custom server block
rm -rf /etc/nginx/http.d/* /etc/nginx/conf.d/* || true

# Update Nginx port if PORT environment variable is set by Railway (defaulting to 8080)
PORT="${PORT:-8080}"
echo "Binding Nginx to port ${PORT}..."
sed -i "s/listen 8080 default_server;/listen ${PORT} default_server;/g" /etc/nginx/nginx.conf

# Generate temporary APP_KEY if not set in Railway environment variables
if [ -z "$APP_KEY" ]; then
    echo "No APP_KEY found, generating runtime application key..."
    export APP_KEY=$(php artisan key:generate --show)
fi

# Ensure SQLite database exists if using SQLite
if [ "$DB_CONNECTION" = "sqlite" ] || [ -z "$DB_CONNECTION" ]; then
    mkdir -p /app/database
    touch /app/database/database.sqlite
    chmod 777 /app/database/database.sqlite
fi

# Ensure storage & cache directory permissions
mkdir -p /app/storage/app/public /app/storage/framework/cache /app/storage/framework/sessions /app/storage/framework/views /app/bootstrap/cache
chmod -R 777 /app/storage /app/bootstrap/cache /app/database

# Publish Filament CSS & JS assets to public directory
php artisan filament:assets || true

# Create storage symlink for uploaded media and avatars
php artisan storage:link --force || true

# Run database migrations
php artisan migrate --force || true

# Clear cached config so dynamic runtime env vars are read cleanly
php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true

# Run Laravel & Filament optimizations for production panel caching
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true
php artisan filament:optimize || true

# Start process manager
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
