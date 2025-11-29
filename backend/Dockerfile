FROM php:8.4-fpm-alpine

# Install nginx and system dependencies
RUN apk add --no-cache \
    nginx \
    git \
    curl \
    libpng-dev \
    oniguruma-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    postgresql-dev \
    icu-dev

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd zip intl

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy application
COPY . .

# Install dependencies
RUN composer install --no-dev --optimize-autoloader

# Fix permissions
RUN chmod -R 777 storage bootstrap/cache

# Create storage link
RUN php artisan storage:link

# Cache configuration moved to start.sh for runtime env vars


# Copy nginx configuration
COPY nginx.conf /etc/nginx/http.d/default.conf

# Copy start script
COPY start.sh /start.sh
RUN chmod +x /start.sh

# Expose port 80
EXPOSE 80

# Start both PHP-FPM and Nginx
CMD ["/start.sh"]