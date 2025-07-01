#!/bin/bash
set -e

echo "🚀 Starting VidaduAcademy Laravel Backend on Render..."

# We're already in the backend directory when Render runs this
# No need to cd anywhere - we're at /opt/render/project/src/

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
    echo "✅ DATABASE_URL is set, testing connection..."
    
    # Test database connection with shorter timeout for faster startup
    for i in {1..5}; do
        if timeout 5 php artisan migrate:status >/dev/null 2>&1; then
            echo "✅ Database connection: OK"
            DATABASE_OK=true
            break
        else
            echo "Database not ready, waiting... ($i/5)"
            if [ $i -eq 5 ]; then
                echo "❌ Database connection failed after 5 attempts"
                echo "🔄 Falling back to SQLite for faster startup..."
                
                # Fallback to SQLite
                cat >> .env << EOF

# Fallback database configuration
DB_CONNECTION=sqlite
DB_DATABASE=/opt/render/project/src/backend/database/database.sqlite
EOF
                mkdir -p database
                touch database/database.sqlite
                chmod 664 database/database.sqlite
                DATABASE_OK=false
                break
            fi
            sleep 2
        fi
    done
fi

# Start web server immediately - don't wait for migrations
echo "🚀 Starting web server (parallel with database setup)..."

# Background process for database setup
(
    if [ "$DATABASE_OK" = "true" ] || [ -z "$DATABASE_OK" ]; then
        echo "📊 Running database migrations in background..."
        if php artisan migrate --force >/dev/null 2>&1; then
            echo "✅ Migrations completed successfully"
        else
            echo "❌ Migrations failed, check logs"
        fi
        
        # Create admin user
        echo "👤 Creating admin user..."
        php create_admin.php >/dev/null 2>&1 || echo "Admin user creation failed"
    fi
) &

# Run Laravel caching (quick operations)
echo "⚡ Running Laravel setup..."
php artisan config:cache >/dev/null 2>&1 || echo "Config cache failed"
php artisan route:cache >/dev/null 2>&1 || echo "Route cache failed"  
php artisan view:cache >/dev/null 2>&1 || echo "View cache failed"

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
