# Stop execution on any error
set -e

# Define colors for output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${YELLOW}Starting update process...${NC}"

# 1. Pull the latest changes from Git
echo -e "${GREEN}Pulling latest changes from Git...${NC}"
git pull origin main

# 2. Rebuild the Docker containers
# Force rebuild of web container to ensure frontend assets are updated
echo -e "${GREEN}Building updated Docker images (forcing web rebuild)...${NC}"
docker compose -f docker-compose.prod.yml build --no-cache web
docker compose -f docker-compose.prod.yml build app worker scheduler

# 3. Restart services
# 'up -d' will recreate containers only if configuration or image changed
echo -e "${GREEN}Restarting services...${NC}"
docker compose -f docker-compose.prod.yml up -d --remove-orphans

# 4. Run database migrations (optional but recommended)
echo -e "${GREEN}Running database migrations...${NC}"
docker compose -f docker-compose.prod.yml exec app php artisan migrate --force

# 5. Clear caches
echo -e "${GREEN}Clearing application caches...${NC}"
docker compose -f docker-compose.prod.yml exec app php artisan optimize:clear
docker compose -f docker-compose.prod.yml exec app php artisan config:cache
docker compose -f docker-compose.prod.yml exec app php artisan route:cache
docker compose -f docker-compose.prod.yml exec app php artisan view:cache

# 6. Clean up unused images to save space
echo -e "${GREEN}Cleaning up unused Docker images...${NC}"
docker image prune -f

echo -e "${GREEN}Update completed successfully!${NC}"
