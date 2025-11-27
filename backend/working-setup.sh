#!/bin/bash

echo "🚀 Complete Working Setup"

# Stop and clean
docker-compose down -v

# Fix permissions on host
echo "🔧 Setting up permissions..."
sudo chmod -R 775 storage bootstrap/cache
sudo chown -R $USER:www-data storage bootstrap/cache
mkdir -p storage/framework/{sessions,views,cache}
sudo chmod -R 775 storage/framework
sudo chown -R $USER:www-data storage/framework

# Build
echo "🐳 Building containers..."
docker-compose up --build -d

# Wait for MySQL
echo "⏳ Waiting for MySQL..."
while ! docker-compose exec db mysqladmin ping -h localhost --silent; do
    sleep 5
done
echo "✅ MySQL is ready!"

# Fix permissions inside container
echo "📁 Fixing container permissions..."
docker-compose exec app bash -c "
    sudo chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
    chmod -R 775 /var/www/storage /var/www/bootstrap/cache
"

# Install dependencies
echo "📦 Installing dependencies..."
docker-compose exec app composer install --no-interaction --prefer-dist

# Generate key
echo "🔑 Generating application key..."
docker-compose exec app php artisan key:generate

# Clear and cache config
echo "⚙️ Caching configuration..."
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan config:cache

# Fresh migration (drop all tables and re-run)
echo "🗄️ Running fresh migrations..."
docker-compose exec app php artisan migrate:fresh --force

# Seed database
echo "🌱 Seeding database..."
docker-compose exec app php artisan db:seed --force

echo "✅ Setup completed successfully!"
echo "🌐 Backend API: http://localhost:8000"
echo "🎉 You can now access your Laravel application!"