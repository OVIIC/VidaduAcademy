#!/usr/bin/env bash
# Render.com build script for Laravel

echo "🚀 Starting Render.com build for VidaduAcademy Laravel Backend..."

# Set PHP version
export PHP_VERSION="8.3"

# Navigate to backend directory
cd backend || exit 1

# Set proper permissions for Laravel
echo "🔒 Setting Laravel permissions..."
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true

# Install dependencies with platform requirement bypass for deployment compatibility
echo "📦 Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction --ignore-platform-reqs

# Generate application key if not exists
echo "🔑 Generating application key..."
php artisan key:generate --force --no-interaction

# Clear all caches first
echo "🧹 Clearing Laravel caches..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Set proper permissions again after vendor install
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true

# Cache Laravel configuration
echo "⚡ Caching Laravel configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "✅ Build completed successfully!"
