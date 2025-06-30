# Railway Environment Variables Template
# Copy these to Railway dashboard Variables section

# Basic Laravel Settings
APP_NAME=VidaduAcademy
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:GENERATE_WITH_ARTISAN_KEY_GENERATE

# Database (Railway will provide these automatically)
DATABASE_URL=${{MySQL.DATABASE_URL}}
DB_CONNECTION=mysql

# Cache & Session (simple file-based for Railway)
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync

# Logging
LOG_CHANNEL=stack
LOG_LEVEL=error
