#!/usr/bin/env bash
# Render.com build script for Laravel

echo "🚀 Starting Render.com build for VidaduAcademy Laravel Backend..."

# Set PHP version
export PHP_VERSION="8.3"

# Install dependencies with platform requirement bypass for deployment compatibility
echo "📦 Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction --ignore-platform-reqs

# Cache Laravel configuration
echo "⚡ Caching Laravel configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "✅ Build completed successfully!"
