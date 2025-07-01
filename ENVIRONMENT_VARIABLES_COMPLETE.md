# VidaduAcademy Environment Variables Complete Guide
# ================================================================

## 🎯 RENDER.COM DASHBOARD ENVIRONMENT VARIABLES
# Copy these to your Render service environment section

# ===== CORE APPLICATION =====
APP_NAME=VidaduAcademy
APP_ENV=production
APP_DEBUG=false
APP_URL=https://vidaduacademy.onrender.com
APP_KEY=base64:wUAdCvxjj0YV4Kcs9ML+Qc3vKU69u9qZcuQlvfyB9aM=

# ===== FRONTEND CONFIGURATION =====
FRONTEND_URL=https://vidaduacademy-frontend.onrender.com
VITE_API_BASE_URL=https://vidaduacademy.onrender.com
VITE_APP_NAME=VidaduAcademy

# ===== LOGGING & DEBUGGING =====
LOG_CHANNEL=stderr
LOG_LEVEL=error
LOG_STACK=stderr

# ===== DATABASE (PostgreSQL) =====
DB_CONNECTION=pgsql
DATABASE_URL=postgresql://username:password@host:port/database_name
# Note: DATABASE_URL should be automatically set by Render when you connect PostgreSQL service

# ===== CACHE & SESSION =====
CACHE_DRIVER=file
CACHE_PREFIX=vidadu_prod
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=true
SESSION_PATH=/
SESSION_DOMAIN=.onrender.com
SESSION_SECURE_COOKIES=true

# ===== QUEUE SYSTEM =====
QUEUE_CONNECTION=database
QUEUE_FAILED_DRIVER=database-uuids

# ===== MAIL CONFIGURATION =====
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@vidaduacademy.com
MAIL_FROM_NAME=VidaduAcademy

# ===== STRIPE PAYMENT =====
STRIPE_KEY=pk_live_YOUR_LIVE_PUBLISHABLE_KEY
STRIPE_SECRET=sk_live_YOUR_LIVE_SECRET_KEY
STRIPE_WEBHOOK_SECRET=whsec_YOUR_WEBHOOK_SECRET

# ===== SECURITY =====
SANCTUM_STATEFUL_DOMAINS=vidaduacademy-frontend.onrender.com,vidaduacademy.onrender.com
SESSION_DOMAIN=.onrender.com

# ===== FILE STORAGE =====
FILESYSTEM_DISK=public
AWS_ACCESS_KEY_ID=your-aws-key
AWS_SECRET_ACCESS_KEY=your-aws-secret
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=vidaduacademy-storage

# ===== PERFORMANCE =====
OPCACHE_ENABLE=1
OPCACHE_MEMORY_CONSUMPTION=256
OPCACHE_MAX_ACCELERATED_FILES=20000

# ===== TIMEZONE =====
APP_TIMEZONE=Europe/Bratislava
