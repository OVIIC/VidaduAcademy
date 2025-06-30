# 🎨 Render Deployment Guide - VidaduAcademy

Complete guide for deploying VidaduAcademy (Laravel + Vue.js) on Render.com

## 🚀 Quick Start

### Prerequisites
- GitHub account with VidaduAcademy repository
- Render.com account (free)

### 1. Backend Deployment (Laravel API)

#### Step 1: Create Web Service
1. Go to [Render Dashboard](https://dashboard.render.com)
2. Click **"New +"** → **"Web Service"**
3. Connect your GitHub repository
4. Configure:
   - **Name**: `vidaduacademy-backend`
   - **Region**: `Frankfurt (Europe)`
   - **Branch**: `main`
   - **Root Directory**: `backend`
   - **Environment**: `Docker`
   - **Dockerfile Path**: `backend/Dockerfile.render`

#### Step 2: Set Environment Variables
In Render dashboard, add these environment variables:

```bash
# Required
APP_NAME=VidaduAcademy
APP_ENV=production
APP_DEBUG=false
APP_KEY=                    # Generate after first deploy

# Database (auto-provided by Render PostgreSQL)
DATABASE_URL=${{DATABASE_URL}}
DB_CONNECTION=pgsql

# Cache & Session
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync

# Logging
LOG_CHANNEL=stack
LOG_LEVEL=error

# Build optimization
COMPOSER_PROCESS_TIMEOUT=600
COMPOSER_MEMORY_LIMIT=2G
```

#### Step 3: Add PostgreSQL Database
1. In your service dashboard, click **"Environment"**
2. Click **"New Database"** → **"PostgreSQL"**
3. Name: `vidaduacademy-db`
4. The `DATABASE_URL` will be automatically provided

### 2. Frontend Deployment (Vue.js)

#### Step 1: Create Static Site
1. Click **"New +"** → **"Static Site"**
2. Connect same GitHub repository
3. Configure:
   - **Name**: `vidaduacademy-frontend`
   - **Branch**: `main`
   - **Root Directory**: `frontend`
   - **Build Command**: `npm install && npm run build`
   - **Publish Directory**: `dist`

#### Step 2: Set Environment Variables
```bash
NODE_ENV=production
VITE_API_URL=https://vidaduacademy-backend.onrender.com
```

### 3. Post-Deployment Setup

#### Generate APP_KEY
After backend is deployed, generate Laravel APP_KEY:

1. Go to backend service → **"Shell"**
2. Run: `php artisan key:generate --show`
3. Copy the generated key
4. Add to environment variables as `APP_KEY=base64:your-generated-key`

#### Run Database Migrations
1. In backend service shell:
```bash
php artisan migrate --force
php artisan db:seed --force  # Optional: add sample data
```

## 🔧 Configuration Files

### Backend Dockerfile (`backend/Dockerfile.render`)
```dockerfile
FROM php:8.1.31-apache
# ... PostgreSQL support
# ... Render-specific configurations
```

### Render Blueprint (`render.yaml`)
```yaml
services:
  - type: web
    name: vidaduacademy-backend
    env: docker
    dockerfilePath: ./backend/Dockerfile.render
    # ... complete configuration
```

## 🌐 Domain Configuration

### Custom Domains (Optional)
1. **Backend**: `api.yourdomain.com`
2. **Frontend**: `app.yourdomain.com` or `yourdomain.com`

### CORS Configuration
Update `SANCTUM_STATEFUL_DOMAINS` with your frontend domain:
```bash
SANCTUM_STATEFUL_DOMAINS=yourdomain.com,app.yourdomain.com
FRONTEND_URL=https://app.yourdomain.com
```

## 📊 Free Tier Limits

### Render Free Tier
- **Static Sites**: Unlimited
- **Web Services**: 750 hours/month
- **Databases**: 1 PostgreSQL (90 days retention)
- **Bandwidth**: 100GB/month
- **Build Time**: 500 minutes/month

### Sleep Policy
- Web services sleep after 30 minutes of inactivity
- Wake up on first request (cold start ~30-60 seconds)

## 🛠️ Troubleshooting

### Common Issues

#### 1. Build Failures
- Check build logs in Render dashboard
- Verify Dockerfile path
- Check composer/npm dependencies

#### 2. Database Connection
- Ensure `DATABASE_URL` is set
- Verify PostgreSQL service is running
- Check migrations status

#### 3. Frontend API Calls
- Verify `VITE_API_URL` points to correct backend
- Check CORS configuration
- Ensure backend is awake

### Debug Commands
```bash
# Backend shell
php artisan config:clear
php artisan migrate:status
php artisan queue:work --once

# Check logs
tail -f storage/logs/laravel.log
```

## 🔒 Security Checklist

- [ ] `APP_DEBUG=false` in production
- [ ] Strong `APP_KEY` generated
- [ ] Database credentials secure
- [ ] HTTPS enforced (automatic on Render)
- [ ] CORS properly configured
- [ ] Environment variables set

## 📈 Performance Tips

1. **Enable Laravel Caching**:
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

2. **Frontend Optimizations**:
- Vite automatically optimizes for production
- Static site delivers via CDN

3. **Database Optimization**:
- Use database indexes (already in migrations)
- Consider connection pooling for high traffic

## 🎯 Production Checklist

### Before Going Live
- [ ] Test all API endpoints
- [ ] Verify frontend-backend communication
- [ ] Test user registration/login
- [ ] Test course purchase flow
- [ ] Set up monitoring/logging
- [ ] Configure backups
- [ ] Set up custom domains

### Monitoring
- Render provides basic metrics
- Set up error tracking (Sentry, etc.)
- Monitor performance and uptime

## 🚀 Deployment Commands

### Automatic Deployment
- Pushes to `main` branch automatically trigger deployments
- Both frontend and backend update independently

### Manual Deployment
- Use Render dashboard "Deploy Latest Commit"
- Or trigger via API/webhooks

## 📱 Mobile & PWA
The Vue.js frontend is responsive and can be enhanced with PWA features for mobile app-like experience.

---

## 🆘 Support

- **Render Docs**: https://render.com/docs
- **Laravel Docs**: https://laravel.com/docs
- **Vue.js Docs**: https://vuejs.org/guide/

---

**🎉 Your VidaduAcademy is now live on Render!**

Frontend: `https://vidaduacademy-frontend.onrender.com`
Backend: `https://vidaduacademy-backend.onrender.com`
