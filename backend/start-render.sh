#!/bin/bash
set -e

echo "🚀 Starting VidaduAcademy Laravel Backend on Render..."

# Navigate to backend directory
cd backend || cd /opt/render/project/src/backend || cd .

# Fix storage permissions (critical for Laravel)
echo "🔒 Setting Laravel storage permissions..."
chmod -R 775 storage bootstrap/cache 2>/dev/null || true
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true

# Create required directories if they don't exist
mkdir -p storage/logs storage/framework/cache storage/framework/sessions storage/framework/views
chmod -R 775 storage bootstrap/cache

# Generate APP_KEY if not set or invalid
echo "🔑 Checking/generating APP_KEY..."
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "base64:YOUR_APP_KEY_HERE" ]; then
    echo "Generating new APP_KEY..."
    export APP_KEY=$(php artisan key:generate --show)
    echo "Generated APP_KEY: $APP_KEY"
fi

# Create .env file from environment variables
echo "📝 Creating .env file from environment variables..."
cat > .env << EOF
APP_NAME=${APP_NAME:-VidaduAcademy}
APP_ENV=${APP_ENV:-production}
APP_KEY=${APP_KEY}
APP_DEBUG=${APP_DEBUG:-false}
APP_URL=${RENDER_EXTERNAL_URL:-http://localhost}

LOG_CHANNEL=${LOG_CHANNEL:-stack}
LOG_LEVEL=${LOG_LEVEL:-error}

DB_CONNECTION=${DB_CONNECTION:-pgsql}
DATABASE_URL=${DATABASE_URL}

CACHE_DRIVER=${CACHE_DRIVER:-file}
SESSION_DRIVER=${SESSION_DRIVER:-file}
QUEUE_CONNECTION=${QUEUE_CONNECTION:-sync}

STRIPE_KEY=${STRIPE_KEY}
STRIPE_SECRET=${STRIPE_SECRET}
STRIPE_WEBHOOK_SECRET=${STRIPE_WEBHOOK_SECRET}
EOF

# Wait for database connection with better error handling
echo "⏳ Checking database connection..."

# Check if DATABASE_URL is properly set
if [ -z "$DATABASE_URL" ] || [ "$DATABASE_URL" = "postgresql://username:password@host:port/database" ]; then
    echo "⚠️  DATABASE_URL not properly set!"
    echo "📋 Please set DATABASE_URL in Render Dashboard:"
    echo "   1. Go to Render Dashboard"
    echo "   2. Connect PostgreSQL service to your web service"
    echo "   3. DATABASE_URL will be automatically set"
    echo ""
    echo "🔄 Using SQLite as fallback for now..."
    
    # Fallback to SQLite if PostgreSQL not available
    cat >> .env << EOF

# Fallback database configuration
DB_CONNECTION=sqlite
DB_DATABASE=/opt/render/project/src/backend/database/database.sqlite
EOF
    
    # Create SQLite database
    mkdir -p database
    touch database/database.sqlite
    chmod 664 database/database.sqlite
else
    echo "✅ DATABASE_URL is set, testing connection..."
    
    # Test database connection with timeout
    for i in {1..10}; do
        if timeout 10 php artisan migrate:status >/dev/null 2>&1; then
            echo "✅ Database connection: OK"
            break
        else
            echo "Database not ready, waiting... ($i/10)"
            if [ $i -eq 10 ]; then
                echo "❌ Database connection failed after 10 attempts"
                echo "🔄 Falling back to SQLite..."
                
                # Fallback to SQLite
                cat >> .env << EOF

# Fallback database configuration
DB_CONNECTION=sqlite
DB_DATABASE=/opt/render/project/src/backend/database/database.sqlite
EOF
                mkdir -p database
                touch database/database.sqlite
                chmod 664 database/database.sqlite
                break
            fi
            sleep 3
        fi
    done
fi

# Run Laravel setup
echo "⚡ Running Laravel setup..."
php artisan config:cache || echo "Config cache failed, continuing..."
php artisan route:cache || echo "Route cache failed, continuing..."
php artisan view:cache || echo "View cache failed, continuing..."

# Critical: Run migrations and ensure they succeed
echo "📊 Running database migrations..."
if php artisan migrate --force; then
    echo "✅ Migrations completed successfully"
else
    echo "❌ Migrations failed! Checking database..."
    php artisan migrate:status || true
    exit 1
fi

# Create admin user if needed
echo "👤 Creating admin user..."
php create_admin.php || echo "Admin user creation failed, continuing..."

echo "🎨 Starting Apache server on Render..."
# Start Apache on dynamic port
export APACHE_RUN_USER=www-data
export APACHE_RUN_GROUP=www-data
export APACHE_LOG_DIR=/var/log/apache2
export APACHE_LOCK_DIR=/var/lock/apache2
export APACHE_PID_FILE=/var/run/apache2.pid

# Configure Apache for Render dynamic PORT
sed -i "s/Listen 80/Listen ${PORT:-80}/" /etc/apache2/ports.conf || true
sed -i "s/:80/:${PORT:-80}/" /etc/apache2/sites-available/000-default.conf || true

# Start Apache in foreground
exec apache2-foreground
