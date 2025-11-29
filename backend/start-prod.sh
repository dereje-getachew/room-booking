#!/bin/sh

# Debug environment variables
echo "=== DEBUG INFO ==="
echo "APP_ENV: ${APP_ENV:-not set}"
echo "APP_KEY: ${APP_KEY:+set}"
echo "DB_CONNECTION: ${DB_CONNECTION:-not set}"
echo "DB_HOST: ${DB_HOST:-not set}"
echo "DB_DATABASE: ${DB_DATABASE:-not set}"
echo "=== END DEBUG ==="

#############################################
# STREAM LARAVEL LOGS INTO RENDER LOGS
#############################################
echo "Starting Laravel log stream..."
mkdir -p storage/logs
touch storage/logs/laravel.log
tail -n 0 -f storage/logs/laravel.log &
#############################################

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

#############################################
# STREAM PHP-FPM ERRORS INTO RENDER LOGS
#############################################
echo "log_errors=On"      > /usr/local/etc/php/conf.d/log.conf
echo "error_log=/dev/stderr" >> /usr/local/etc/php/conf.d/log.conf
#############################################

# Start Supervisor
echo "Starting Supervisor..."
/usr/bin/supervisord -c /etc/supervisord.conf
