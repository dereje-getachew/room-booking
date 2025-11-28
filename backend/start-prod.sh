#!/bin sh

# Generate app key if not exists
if [ -z "$APP_KEY" ]; then
    echo "Generating APP_KEY..."
    php artisan key:generate --force
fi

# Run database migrations
echo "Running database migrations..."
php artisan migrate --force

# Cache configuration for production at runtime
echo "Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Start Supervisor
echo "Starting Supervisor..."
/usr/bin/supervisord -c /etc/supervisord.conf
