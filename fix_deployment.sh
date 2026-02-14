#!/bin/bash

echo "Starting deployment fix..."

# 1. Ensure storage directories exist
echo "Creating missing directories in backend/storage..."
mkdir -p backend/storage/framework/cache/data
mkdir -p backend/storage/framework/sessions
mkdir -p backend/storage/framework/views
mkdir -p backend/storage/logs
mkdir -p backend/bootstrap/cache

# 2. Fix permissions (777 to be safe for Docker mounts)
echo "Setting permissions..."
chmod -R 777 backend/storage
chmod -R 777 backend/bootstrap/cache

# 3. Add .gitignore back if missing (to ensure structure is kept)
touch backend/storage/logs/.gitkeep
touch backend/bootstrap/cache/.gitkeep

# 4. Clear cache and run migrations inside the container
echo "Clearing application cache and running migrations..."
docker compose -f docker-compose.prod.yml exec -T app php artisan optimize:clear
docker compose -f docker-compose.prod.yml exec -T app php artisan migrate --force

echo "Deployment fix complete! Please refresh your browser."
