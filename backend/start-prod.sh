#!/bin/sh

# Debug environment variables
echo "=== DEBUG INFO ==="
echo "APP_ENV: ${APP_ENV:-not set}"
echo "APP_KEY: ${APP_KEY:+set}"
echo "DB_CONNECTION: ${DB_CONNECTION:-not set}"
echo "DB_HOST: ${DB_HOST:-not set}"
echo "DB_DATABASE: ${DB_DATABASE:-not set}"
echo "=== END DEBUG ==="

# Generate app key if not exists
if [ -z "$APP_KEY" ]; then
    echo "Generating APP_KEY..."
    php artisan key:generate --force
fi

# Test basic Laravel functionality
echo "Testing Laravel installation..."
php artisan --version || exit 1

# Test database connection before migrations
echo "Testing database connection..."
php artisan db:show || echo "Database connection failed"

# Run database migrations
echo "Running database migrations..."
php artisan migrate --force || echo "Migration failed"

# Cache configuration for production at runtime
echo "Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Start Supervisor
echo "Starting Supervisor..."
/usr/bin/supervisord -c /etc/supervisord.conf
