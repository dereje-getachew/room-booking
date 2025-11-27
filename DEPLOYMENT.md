# Render Deployment Guide

This guide explains how to deploy your Room Booking System to Render using Docker.

## Prerequisites

1. Git repository (GitHub, GitLab, or Bitbucket)
2. Render account (sign up at render.com)
3. Push your code to the repository

## Deployment Steps

### 1. Prepare Your Repository

Make sure all the deployment files are committed:
```bash
git add .
git commit -m "Add Render deployment configuration"
git push
```

### 2. Create PostgreSQL Database on Render

1. Go to Render Dashboard
2. Click "New +" → "PostgreSQL"
3. Configure:
   - **Name**: `booking-db`
   - **Database**: `booking_system`
   - **User**: `booking_user`
   - **Region**: Choose closest to you
   - **Plan**: Free
4. Click "Create Database"
5. **Save the connection details** - you'll need them!

### 3. Deploy Backend (Laravel)

1. Go to Render Dashboard
2. Click "New +" → "Web Service"
3. Connect your repository
4. Configure:
   - **Name**: `booking-system-backend`
   - **Environment**: Docker
   - **Region**: Same as database
   - **Branch**: main
   - **Dockerfile Path**: `./backend/Dockerfile.production`
   - **Plan**: Free

5. Add Environment Variables:
   ```
   APP_NAME=RoomBooker
   APP_ENV=production
   APP_KEY=  # Generate with: php artisan key:generate --show
   APP_DEBUG=false
   APP_URL=https://booking-system-backend.onrender.com
   
   DB_CONNECTION=pgsql
   DB_HOST=  # From PostgreSQL connection info
   DB_PORT=5432
   DB_DATABASE=booking_system
   DB_USERNAME=booking_user
   DB_PASSWORD=  # From PostgreSQL connection info
   
   FRONTEND_URL=https://booking-system-frontend.onrender.com
   SANCTUM_STATEFUL_DOMAINS=booking-system-frontend.onrender.com
   SESSION_DRIVER=cookie
   SESSION_DOMAIN=.onrender.com
   
   LOG_CHANNEL=stderr
   QUEUE_CONNECTION=sync
   ```

6. Click "Create Web Service"

### 4. Run Migrations

After backend deploys successfully:
1. Go to backend service → Shell
2. Run:
   ```bash
   php artisan migrate --force
   php artisan storage:link
   ```

### 5. Deploy Frontend (Next.js)

1. Go to Render Dashboard
2. Click "New +" → "Web Service"
3. Connect your repository
4. Configure:
   - **Name**: `booking-system-frontend`
   - **Environment**: Docker
   - **Region**: Same as backend
   - **Branch**: main
   - **Dockerfile Path**: `./frontend/Dockerfile`
   - **Plan**: Free

5. Add Environment Variables:
   ```
   NEXT_PUBLIC_API_URL=https://booking-system-backend.onrender.com
   ```

6. Click "Create Web Service"

## Important Notes

### Free Tier Limitations

- Services spin down after 15 minutes of inactivity
- First request after spin-down takes ~30 seconds to wake up
- Database has 90-day expiry (need to recreate)

### Generate APP_KEY

On your local machine:
```bash
cd backend
php artisan key:generate --show
```
Copy the output and use it as `APP_KEY` environment variable.

### CORS Configuration

The backend is already configured for CORS. Make sure `FRONTEND_URL` in backend matches your actual frontend URL.

### Storage

File uploads (room images) will NOT persist on free tier. For production, use:
- AWS S3
- Cloudinary
- Other cloud storage

## Updating Your App

```bash
git add .
git commit -m "Update"
git push
```

Render auto-deploys on push to main branch.

## Troubleshooting

### Backend not starting
- Check logs in Render dashboard
- Verify all environment variables are set
- Ensure APP_KEY is set correctly

### Database connection failed
- Verify DB credentials from PostgreSQL service
- Check DB_HOST includes the full hostname
- Ensure region matches

### Frontend can't connect to backend
- Verify NEXT_PUBLIC_API_URL is correct
- Check backend service is running
- Test backend URL directly

### CORS errors
- Ensure FRONTEND_URL in backend matches actual frontend URL
- Check SANCTUM_STATEFUL_DOMAINS includes frontend domain
- Verify SESSION_DOMAIN is set to `.onrender.com`

## Next Steps

1. **Custom Domain**: Add custom domain in Render dashboard
2. **Environment Secrets**: Use Render's secret management
3. **Monitoring**: Set up health checks and alerts
4. **Backups**: Export database regularly

## Useful Commands

Run in backend Shell on Render:

```bash
# Run migrations
php artisan migrate --force

# Create storage link
php artisan storage:link

# Clear caches
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Check status
php artisan about
```
