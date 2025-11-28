#!/bin/sh

# Cache configuration for production at runtime
echo "Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Start Supervisor
echo "Starting Supervisor..."
/usr/bin/supervisord -c /etc/supervisord.conf
