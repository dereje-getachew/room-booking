#!/bin/bash

# cPanel Laravel Deployment Script
# Usage: ./deploy-cpanel.sh

echo "🚀 Starting Laravel cPanel Deployment..."

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Check if we're in the right directory
if [ ! -f "artisan" ]; then
    print_error "artisan file not found. Please run this script from the Laravel root directory."
    exit 1
fi

# Install dependencies
print_status "Installing Composer dependencies..."
composer install --optimize-autoloader --no-dev --no-interaction

if [ $? -ne 0 ]; then
    print_error "Composer install failed"
    exit 1
fi

# Check if .env file exists
if [ ! -f ".env" ]; then
    print_warning ".env file not found. Creating from example..."
    if [ -f "env-production-example" ]; then
        cp env-production-example .env
        print_warning "Please update .env file with your production settings before continuing."
        exit 1
    else
        cp .env.example .env
        print_warning "Please update .env file with your production settings."
        exit 1
    fi
fi

# Generate app key if not set
if ! grep -q "APP_KEY=base64:" .env; then
    print_status "Generating application key..."
    php artisan key:generate --force
fi

# Run database migrations
print_status "Running database migrations..."
php artisan migrate --force --no-interaction

if [ $? -ne 0 ]; then
    print_error "Database migration failed"
    exit 1
fi

# Optimize application
print_status "Optimizing application..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Clear and cache again
print_status "Clearing and recaching..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Re-optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set permissions
print_status "Setting proper permissions..."
chmod 755 storage
chmod 755 bootstrap/cache
chmod -R 755 storage/*
chmod -R 755 bootstrap/cache/*

# Create storage link if not exists
if [ ! -L "public/storage" ]; then
    print_status "Creating storage symbolic link..."
    php artisan storage:link
fi

print_status "✅ Deployment completed successfully!"
print_status "📝 Next steps:"
echo "   1. Update your .env file with correct database credentials"
echo "   2. Point your domain/subdomain to the 'public' directory"
echo "   3. Test your application"
echo "   4. Set up cron job for scheduled tasks if needed"
