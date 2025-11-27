# Render.com Laravel Deployment Guide

## Prerequisites
- Free Render.com account
- GitHub repository with your code
- MySQL database (Render provides free PostgreSQL)

## Step 1: Prepare Your Laravel App

### 1.1 Update .env for Render
```env
APP_NAME="Booking System"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-app-name.onrender.com

# Database (Render PostgreSQL)
DB_CONNECTION=pgsql
DB_HOST=${RENDER_DB_HOST}
DB_PORT=${RENDER_DB_PORT}
DB_DATABASE=${RENDER_DB_NAME}
DB_USERNAME=${RENDER_DB_USER}
DB_PASSWORD=${RENDER_DB_PASSWORD}

# Cache & Session
CACHE_STORE=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

# Redis (Render provides)
REDIS_HOST=${REDIS_HOST}
REDIS_PASSWORD=${REDIS_PASSWORD}
REDIS_PORT=${REDIS_PORT}
```

### 1.2 Add Render-specific Files

#### Create `render.yaml`
```yaml
services:
  - type: web
    name: booking-backend
    env: php
    plan: free
    buildCommand: "composer install --no-dev && php artisan config:cache && php artisan route:cache"
    startCommand: "php artisan serve --host=0.0.0.0 --port=$PORT"
    envVars:
      - key: APP_ENV
        value: production
      - key: APP_DEBUG
        value: false
      - key: APP_KEY
        generateValue: true
      - key: APP_URL
        sync: false
    autoDeploy: true

databases:
  - name: booking-db
    plan: free
    databaseName: booking
    user: booking

  - name: booking-redis
    plan: free
    ipAllowList: []
```

#### Update `composer.json` for PostgreSQL
```json
{
  "require": {
    "php": "^8.2",
    "laravel/framework": "^12.0",
    "laravel/sanctum": "^4.0",
    "laravel/tinker": "^2.10.1",
    "php-open-source-saver/jwt-auth": "^2.8",
    "ext-pgsql": "*"
  }
}
```

### 1.3 Update Database Configuration

#### In `config/database.php`
```php
'connections' => [
    'pgsql' => [
        'driver' => 'pgsql',
        'url' => env('DATABASE_URL'),
        'host' => env('DB_HOST', '127.0.0.1'),
        'port' => env('DB_PORT', '5432'),
        'database' => env('DB_DATABASE', 'forge'),
        'username' => env('DB_USERNAME', 'forge'),
        'password' => env('DB_PASSWORD', ''),
        'charset' => 'utf8',
        'prefix' => '',
        'prefix_indexes' => true,
        'search_path' => 'public',
        'sslmode' => 'prefer',
    ],
],
```

## Step 2: Deploy to Render

### 2.1 Push to GitHub
```bash
git add .
git commit -m "Prepare for Render deployment"
git push origin main
```

### 2.2 Deploy on Render
1. Go to [render.com](https://render.com)
2. Click "New +" → "Web Service"
3. Connect your GitHub repository
4. Select the backend folder
5. Render will detect `render.yaml` and configure automatically
6. Click "Create Web Service"

### 2.3 Add Database
1. Go to your service dashboard
2. Click "New +" → "PostgreSQL"
3. Name it `booking-db`
4. Click "Create Database"
5. Connect it to your web service

### 2.4 Add Redis (Optional but recommended)
1. Click "New +" → "Redis"
2. Name it `booking-redis`
3. Click "Create Instance"

## Step 3: Post-Deployment

### 3.1 Run Migrations
Render automatically runs migrations if you add this to `render.yaml`:
```yaml
buildCommand: |
  composer install --no-dev
  php artisan config:cache
  php artisan route:cache
  php artisan migrate --force
```

### 3.2 Get Your API URL
Your API will be available at: `https://your-app-name.onrender.com`

## Step 4: Update Frontend

In your Next.js frontend, update API calls:
```javascript
// Before: http://localhost:8000/api/...
// After: https://your-app-name.onrender.com/api/...
```

## Free Tier Limitations
- **Sleeps after 15 minutes** of inactivity
- **Cold starts** can take 30-60 seconds
- **Limited bandwidth** (750MB/month)
- **No custom domains** on free plan

## Upgrade Options
- **Starter plan** ($7/month): No sleep, custom domains
- **Standard plan** ($25/month): More resources, better performance

## Troubleshooting

### Common Issues:
1. **Database Connection Failed**
   - Check database credentials in Render dashboard
   - Ensure PostgreSQL extension is installed

2. **Migration Errors**
   - Run migrations manually in Render shell
   - Check database exists and is accessible

3. **Slow Loading**
   - Free tier cold starts are normal
   - Consider upgrading for production use

### Render Shell Access:
```bash
# Access your service shell
render shell booking-backend

# Run commands
php artisan migrate:status
php artisan cache:clear
php artisan config:clear
```

## Monitoring
- Check Render dashboard for logs
- Monitor usage in billing section
- Set up health checks if needed
