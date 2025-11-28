#!/bin/bash

# Exit on error
set -e

echo "Preparing backend for Shared Hosting (GoogieHost)..."

# 1. Install Production Dependencies
echo "Installing production dependencies..."
composer install --optimize-autoloader --no-dev

# 2. Clear Caches
echo "Clearing caches..."
php artisan config:clear
php artisan route:clear
php artisan view:clear

# 3. Create Zip Archive
echo "Creating zip archive..."
# Exclude unnecessary files to save space
zip -r booking-backend.zip . -x "*.git*" -x "node_modules/*" -x "tests/*" -x "docker/*" -x "Dockerfile*" -x ".env" -x ".phpunit.cache"

echo "Done! Upload 'booking-backend.zip' to your hosting file manager."
