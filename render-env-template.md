# Render Environment Variables Template
# Copy these to Render Dashboard -> Environment Variables

## Basic Laravel Settings
APP_NAME=VidaduAcademy
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:GENERATE_WITH_ARTISAN_KEY_GENERATE

## Database (Render PostgreSQL - auto-provided)
DATABASE_URL=${{DATABASE_URL}}
DB_CONNECTION=pgsql

## Cache & Session (file-based for Render free tier)
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync

## Logging
LOG_CHANNEL=stack
LOG_LEVEL=error

## CORS & API Settings
SANCTUM_STATEFUL_DOMAINS=your-frontend.onrender.com
FRONTEND_URL=https://your-frontend.onrender.com

## Optional: Stripe (for payments)
STRIPE_SECRET_KEY=sk_test_your_stripe_secret_key
STRIPE_PUBLISHABLE_KEY=pk_test_your_stripe_publishable_key
STRIPE_WEBHOOK_SECRET=whsec_your_webhook_secret
STRIPE_CURRENCY=EUR

## Optional: Mail Settings
MAIL_MAILER=log
MAIL_FROM_ADDRESS="noreply@your-domain.com"
MAIL_FROM_NAME="${APP_NAME}"

## Build Optimization
COMPOSER_PROCESS_TIMEOUT=600
COMPOSER_MEMORY_LIMIT=2G
