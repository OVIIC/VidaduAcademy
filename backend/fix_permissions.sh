#!/bin/bash

# Create storage directories if they don't exist
echo "Creating storage directories..."
mkdir -p storage/framework/{cache,sessions,views}
mkdir -p storage/logs
mkdir -p bootstrap/cache

# Set permissions to read/write/execute for everyone (to avoid user mismatch issues in Docker)
echo "Setting permissions..."
chmod -R 777 storage
chmod -R 777 bootstrap/cache

# Clear cache
echo "Clearing application cache..."
php artisan optimize:clear

echo "Done! Storage is fixed."
