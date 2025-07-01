#!/bin/bash

# Production migration script for Render
echo "🚀 Running production migrations..."

# Set environment
export APP_ENV=production

# Generate application key if not exists
if [ -z "$APP_KEY" ]; then
    echo "⚠️  Generating APP_KEY..."
    php artisan key:generate --force
fi

# Clear various caches
echo "🧹 Clearing caches..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Run migrations
echo "📊 Running database migrations..."
php artisan migrate --force

# Create sessions table if not exists
echo "📝 Ensuring sessions table exists..."
php artisan migrate --path=database/migrations --force

# Seed the database if needed
echo "🌱 Seeding database (if applicable)..."
php artisan db:seed --force || echo "No seeders to run"

# Cache configurations for production
echo "⚡ Caching configurations..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Fix permissions
echo "🔧 Setting permissions..."
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || echo "Permission change skipped (not running as root)"

echo "✅ Production migrations completed successfully!"
