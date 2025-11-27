# cPanel Laravel Deployment Guide

## Prerequisites
- cPanel hosting with PHP 8.2+ support
- MySQL database
- SSH access or File Manager access
- Composer installed on cPanel (usually available via Terminal)

## Step 1: Prepare Your Application

### 1.1 Create Production .env File
```bash
# Copy your .env.example to .env and configure for production
APP_NAME="Booking System"
APP_ENV=production
APP_KEY=your_generated_app_key_here
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Database Configuration
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_cpanel_database_name
DB_USERNAME=your_cpanel_database_user
DB_PASSWORD=your_cpanel_database_password

# Other production settings
SESSION_DRIVER=database
QUEUE_CONNECTION=database
CACHE_STORE=database
LOG_CHANNEL=stack
LOG_LEVEL=error
```

### 1.2 Generate App Key
```bash
php artisan key:generate --force
```

## Step 2: Upload Files to cPanel

### Option A: Using File Manager
1. Compress your backend folder into a ZIP file
2. Upload to cPanel File Manager
3. Extract in the desired directory (usually `public_html/backend`)

### Option B: Using SSH/FTP
```bash
# Upload files directly to your hosting directory
scp -r /home/dere/booking-system/backend/* user@yourdomain.com:public_html/backend/
```

## Step 3: Install Dependencies

### 3.1 Using cPanel Terminal
```bash
cd public_html/backend
composer install --optimize-autoloader --no-dev
```

### 3.2 If Composer Not Available
1. Enable "Terminal" in cPanel
2. Or use "Setup PHP App" feature

## Step 4: Configure Directory Permissions

```bash
# Set proper permissions
chmod 755 storage
chmod 755 bootstrap/cache
chmod -R 755 storage/*
chmod -R 755 bootstrap/cache/*

# Set ownership (if needed)
chown -R yourusername:yourusername storage bootstrap/cache
```

## Step 5: Run Database Migrations

```bash
php artisan migrate --force
```

## Step 6: Optimize Application

```bash
# Cache configuration for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Optimize autoloader
composer dump-autoload --optimize
```

## Step 7: Set Up Document Root

### Option A: Subdirectory Approach
- Point your domain to `public_html/backend/public`
- Access via: `https://yourdomain.com/backend`

### Option B: Subdomain Approach
1. Create subdomain: `api.yourdomain.com`
2. Point document root to `public_html/backend/public`
3. Access via: `https://api.yourdomain.com`

## Step 8: Configure .htaccess

Create/ensure `.htaccess` in `public/` directory:

```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>

# PHP Version (if needed)
<IfModule mod_php.c>
    php_flag display_errors Off
    php_value max_execution_time 300
    php_value memory_limit 256M
</IfModule>
```

## Step 9: Set Up Cron Job (Optional)

For scheduled tasks, add a cron job in cPanel:

```bash
# Run every minute
* * * * * /usr/local/bin/php /home/yourusername/public_html/backend/artisan schedule:run >> /dev/null 2>&1
```

## Step 10: Test Your Deployment

1. Visit your application URL
2. Test API endpoints
3. Check error logs if issues occur

## Troubleshooting

### Common Issues:

1. **500 Internal Server Error**
   - Check storage/logs/laravel.log
   - Verify permissions on storage and bootstrap/cache
   - Ensure .env file is properly configured

2. **Database Connection Failed**
   - Verify database credentials in .env
   - Check if database exists in cPanel
   - Ensure database user has proper permissions

3. **Composer Issues**
   - Clear composer cache: `composer clear-cache`
   - Try `composer install --no-scripts` first

4. **Permission Denied**
   - Ensure proper file permissions (755 for directories, 644 for files)
   - Check ownership of files

## Security Considerations

1. Keep `APP_DEBUG=false` in production
2. Use strong database passwords
3. Regularly update dependencies
4. Monitor error logs
5. Implement rate limiting on API endpoints

## Post-Deployment Maintenance

```bash
# Clear caches when updating
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Re-optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Alternative: Use cPanel's "Setup PHP App"

If your cPanel has the "Setup PHP App" feature:
1. Go to cPanel > Setup PHP App
2. Choose Laravel template (if available)
3. Configure domain and document root
4. Upload files
5. The system will handle most configurations automatically
