#!/bin/bash

# Build script for backend
echo "Building backend..."
cd backend
composer install --no-dev --optimize-autoloader
php artisan config:cache
php artisan route:cache
php artisan view:cache
echo "Backend build complete!"
