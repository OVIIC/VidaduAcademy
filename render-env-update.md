# Render Environment Variables Update

## Critical: Update APP_KEY in Render Dashboard

1. Go to your Render service: https://dashboard.render.com/web/srv-YOUR_SERVICE_ID
2. Navigate to **Environment** tab
3. Update or add the following variable:

```
APP_KEY=base64:wUAdCvxjj0YV4Kcs9ML+Qc3vKU69u9qZcuQlvfyB9aM=
```

## Current Environment Variables Should Include:

```
APP_NAME=VidaduAcademy
APP_ENV=production
APP_DEBUG=false
APP_URL=https://vidaduacademy.onrender.com
APP_KEY=base64:wUAdCvxjj0YV4Kcs9ML+Qc3vKU69u9qZcuQlvfyB9aM=

LOG_CHANNEL=stderr
LOG_LEVEL=error

DB_CONNECTION=pgsql
DATABASE_URL=postgresql://username:password@host:port/database

CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync

STRIPE_KEY=pk_test_YOUR_STRIPE_PUBLISHABLE_KEY
STRIPE_SECRET=sk_test_YOUR_STRIPE_SECRET_KEY
STRIPE_WEBHOOK_SECRET=whsec_YOUR_WEBHOOK_SECRET
```

## After updating APP_KEY:
1. Save the environment variables
2. Render will automatically redeploy
3. Wait for deployment to complete (~5-10 minutes)
4. Test the endpoints

## Test Commands:
```bash
# Health check
curl https://vidaduacademy.onrender.com/api/health

# Admin login
curl -I https://vidaduacademy.onrender.com/admin/login

# API courses
curl https://vidaduacademy.onrender.com/api/courses
```
