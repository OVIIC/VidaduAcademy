# Enhanced Railway Deployment Configuration
# Railway environment variables pro robustný deployment

## Core Environment Variables
```
APP_NAME=VidaduAcademy
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:YOUR_GENERATED_APP_KEY_HERE
APP_URL=https://your-app-domain.railway.app
LOG_CHANNEL=stack
LOG_LEVEL=error
```

## Database Configuration
```
DB_CONNECTION=mysql
DATABASE_URL=mysql://user:password@host:port/database
```

## Cache & Session Configuration
```
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync
```

## Network & Build Optimizations
```
COMPOSER_PROCESS_TIMEOUT=600
COMPOSER_MEMORY_LIMIT=2G
NODE_OPTIONS=--max-old-space-size=4096
NPM_CONFIG_NETWORK_TIMEOUT=300000
NPM_CONFIG_FETCH_RETRY_MINTIMEOUT=20000
NPM_CONFIG_FETCH_RETRY_MAXTIMEOUT=120000
NPM_CONFIG_FETCH_RETRIES=5
```

## Railway Specific Settings
```
NIXPACKS_BUILD_TIMEOUT=1200
NIXPACKS_INSTALL_TIMEOUT=900
RAILWAY_STATIC_URL=https://your-frontend-domain.railway.app
```

## Security Headers
```
SECURE_HEADERS=true
FORCE_HTTPS=true
```

## Build Cache Settings
```
COMPOSER_CACHE_DIR=/tmp/composer-cache
NPM_CONFIG_CACHE=/tmp/npm-cache
```

## Instructions:

1. **Nastavenie v Railway Dashboard:**
   - Choď na railway.app → tvoj projekt
   - Variables tab → pridaj všetky premenné vyššie
   - Vygeneruj APP_KEY pomocou: `php artisan key:generate --show`

2. **Deployment Process:**
   - Railway automaticky detectuje Dockerfile
   - Build prebehne v sekvencii: backend → frontend
   - Database migrácie sa spustia automaticky

3. **Monitoring:**
   - Sleduj Deployments tab pre build logy
   - Metrics tab pre performance
   - Service tab pre runtime logy

4. **Network Troubleshooting:**
   - Build timeout: zvýš NIXPACKS_BUILD_TIMEOUT
   - Network errors: Railway automaticky retries s exponential backoff
   - DNS issues: používame 8.8.8.8 a 8.8.4.4 v Dockerfile

5. **Performance Optimizations:**
   - Composer cache je perzistentný medzi buildmi
   - NPM dependencies sú cached
   - Docker layers sú optimalizované pre caching
