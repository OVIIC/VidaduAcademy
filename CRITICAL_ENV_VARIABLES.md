# 🚨 CRITICAL Environment Variables for Render.com
# ===============================================
# These MUST be set in Render Dashboard for the app to work

## ⚡ IMMEDIATE PRIORITY (Required for basic functionality)
APP_NAME=VidaduAcademy
APP_ENV=production
APP_DEBUG=false
APP_URL=https://vidaduacademy.onrender.com
APP_KEY=base64:wUAdCvxjj0YV4Kcs9ML+Qc3vKU69u9qZcuQlvfyB9aM=

DB_CONNECTION=pgsql
# DATABASE_URL=postgresql://... (auto-set when you connect PostgreSQL service)

LOG_CHANNEL=stderr
LOG_LEVEL=error

## 🔧 MEDIUM PRIORITY (Required for full functionality)  
SESSION_DRIVER=database
CACHE_DRIVER=file
QUEUE_CONNECTION=database

FRONTEND_URL=https://vidaduacademy-frontend.onrender.com
SANCTUM_STATEFUL_DOMAINS=vidaduacademy-frontend.onrender.com,vidaduacademy.onrender.com

## 💳 PAYMENT (Required if using Stripe)
STRIPE_KEY=pk_live_YOUR_LIVE_PUBLISHABLE_KEY
STRIPE_SECRET=sk_live_YOUR_LIVE_SECRET_KEY
STRIPE_WEBHOOK_SECRET=whsec_YOUR_WEBHOOK_SECRET

## 📧 EMAIL (Optional but recommended)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@vidaduacademy.com
MAIL_FROM_NAME=VidaduAcademy

# ===============================================
## 📋 SETUP STEPS:
# 1. Copy IMMEDIATE PRIORITY variables first
# 2. Connect PostgreSQL database (DATABASE_URL auto-set)
# 3. Wait for redeploy (3-5 minutes)
# 4. Test: curl https://vidaduacademy.onrender.com/debug-db
# 5. Add MEDIUM PRIORITY variables
# 6. Configure PAYMENT & EMAIL when ready
