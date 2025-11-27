#!/bin/bash

echo "Setting up Room Booking Backend with fixes..."

# Check if docker is running
if ! docker info > /dev/null 2>&1; then
    echo "Docker is not running. Please start Docker first."
    exit 1
fi

# Stop any running containers
echo "Stopping any running containers..."
docker-compose down

# Build and start containers
echo "Building and starting Docker containers..."
docker-compose up --build -d

# Wait for MySQL to be ready
echo "Waiting for MySQL to be ready..."
while ! docker-compose exec db mysqladmin ping -h localhost --silent; do
    echo "Waiting for MySQL to start..."
    sleep 10
done

echo "MySQL is ready!"

# Install Composer dependencies
echo "Installing Composer dependencies..."
docker-compose exec app composer install --no-interaction --prefer-dist

# Generate app key if not exists
echo "Generating application key..."
docker-compose exec app php artisan key:generate

# Wait a bit more for MySQL to be fully ready
sleep 10

# Run migrations
echo "Running database migrations..."
docker-compose exec app php artisan migrate --force

# Seed the database
echo "Seeding database..."
docker-compose exec app php artisan db:seed --force

echo "Setup completed successfully!"
echo "Backend API: http://localhost:8000"
echo "MySQL: localhost:3306"
