# Railway Deployment Checklist
# Complete checklist for VidaduAcademy Railway deployment

## Pre-Deployment Checklist

### 1. Repository Setup
- [x] Dockerfiles created (backend & frontend)
- [x] .railwayapp.json configured
- [x] .railwayignore optimized
- [x] Environment variables documented
- [x] Health check endpoints implemented

### 2. Backend Configuration
- [x] Laravel Dockerfile multi-stage optimized
- [x] Composer dependencies retry logic
- [x] Apache configuration for production
- [x] Environment variable generation script
- [x] Database migration handling
- [x] Error handling and logging

### 3. Frontend Configuration
- [x] Vue.js/Vite Dockerfile optimized
- [x] NPM install with retry logic
- [x] Nginx configuration for SPA
- [x] Build environment optimization
- [x] Static asset serving

### 4. Railway Environment Variables
Set these in Railway dashboard:

#### Essential Variables
```
APP_NAME=VidaduAcademy
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:GENERATE_WITH_ARTISAN_KEY_GENERATE
APP_URL=https://your-backend-domain.railway.app
```

#### Database
```
DATABASE_URL=mysql://user:password@host:port/database
DB_CONNECTION=mysql
```

#### Performance
```
COMPOSER_PROCESS_TIMEOUT=600
COMPOSER_MEMORY_LIMIT=2G
NODE_OPTIONS=--max-old-space-size=4096
```

#### Cache & Session
```
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync
```

## Deployment Steps

### 1. Create Railway Projects
```bash
# Create backend service
railway login
railway project create
railway service create --name vidadu-backend

# Create frontend service
railway service create --name vidadu-frontend
```

### 2. Connect GitHub Repository
- Connect repository to Railway projects
- Set build configuration to use Dockerfile
- Configure environment variables

### 3. Deploy Backend
- Push code to trigger build
- Generate APP_KEY after first successful build:
  ```bash
  railway run php artisan key:generate --show
  ```
- Update APP_KEY in Railway environment variables
- Verify health endpoint: `/api/health`

### 4. Deploy Frontend
- Configure backend API URL in frontend
- Push code to trigger build
- Verify static assets are served correctly

### 5. Database Setup
- Add MySQL database to Railway project
- Run migrations:
  ```bash
  railway run php artisan migrate --force
  ```
- Seed database if needed:
  ```bash
  railway run php artisan db:seed --force
  ```

## Post-Deployment Verification

### Backend Tests
- [ ] Health endpoint responds: `GET /api/health`
- [ ] API routes accessible
- [ ] Database connection working
- [ ] Authentication endpoints working
- [ ] Course endpoints returning data

### Frontend Tests
- [ ] Application loads correctly
- [ ] SPA routing works (no 404 on refresh)
- [ ] API calls to backend succeed
- [ ] Authentication flow works
- [ ] Course display and purchase flow

## Troubleshooting

### Common Build Issues
1. **Composer timeout**: Increase `COMPOSER_PROCESS_TIMEOUT`
2. **NPM network errors**: Check retry configuration
3. **Memory issues**: Increase `COMPOSER_MEMORY_LIMIT` and `NODE_OPTIONS`
4. **DNS resolution**: Railway networking issues (retry deployment)

### Common Runtime Issues
1. **500 errors**: Check `railway logs` for Laravel errors
2. **Database connection**: Verify `DATABASE_URL` format
3. **Missing APP_KEY**: Generate and set in environment variables
4. **CORS issues**: Configure SANCTUM_STATEFUL_DOMAINS

## Performance Optimization

### Backend
- Enable Laravel caching:
  ```bash
  railway run php artisan config:cache
  railway run php artisan route:cache
  railway run php artisan view:cache
  ```

### Frontend
- Verify Vite build optimization
- Check gzip compression in nginx
- Optimize static asset loading

## Security Checklist
- [ ] APP_DEBUG=false in production
- [ ] Strong APP_KEY generated
- [ ] Database credentials secure
- [ ] HTTPS enforced
- [ ] CORS properly configured
- [ ] Rate limiting enabled

## Monitoring
- Railway dashboard for build/deploy status
- Application logs via `railway logs`
- Health check endpoints
- Performance metrics
- Error tracking

## Backup Strategy
- Database backups via Railway
- Code repository in GitHub
- Environment variables documented
- Deployment configuration versioned
