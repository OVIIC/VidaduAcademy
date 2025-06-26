#!/bin/bash

# VidaduAcademy Setup Script
# This script sets up the complete local development environment

set -e

echo "🚀 Setting up VidaduAcademy - Local-First LMS"
echo "=============================================="

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Check if Docker is running
if ! docker info > /dev/null 2>&1; then
    echo -e "${RED}❌ Docker is not running. Please start Docker and try again.${NC}"
    exit 1
fi

# Check if Node.js is installed
if ! command -v node > /dev/null 2>&1; then
    echo -e "${RED}❌ Node.js is not installed. Please install Node.js 18+ and try again.${NC}"
    exit 1
fi

# Check Node.js version
NODE_VERSION=$(node --version | cut -d'v' -f2 | cut -d'.' -f1)
if [ "$NODE_VERSION" -lt 18 ]; then
    echo -e "${RED}❌ Node.js version 18+ is required. Current version: $(node --version)${NC}"
    exit 1
fi

echo -e "${GREEN}✅ Prerequisites check passed${NC}"

# Setup Backend
echo -e "\n${BLUE}📦 Setting up Laravel Backend...${NC}"
cd backend

# Copy environment file
if [ ! -f .env ]; then
    cp .env.example .env
    echo -e "${GREEN}✅ Environment file created${NC}"
fi

# Install Composer dependencies
if [ ! -d vendor ]; then
    echo -e "${YELLOW}📥 Installing Composer dependencies...${NC}"
    docker run --rm \
        -u "$(id -u):$(id -g)" \
        -v "$(pwd):/var/www/html" \
        -w /var/www/html \
        laravelsail/php82-composer:latest \
        composer install --ignore-platform-reqs
fi

# Generate application key
if ! grep -q "APP_KEY=base64:" .env; then
    echo -e "${YELLOW}🔑 Generating application key...${NC}"
    ./vendor/bin/sail artisan key:generate
fi

# Start Sail containers
echo -e "${YELLOW}🐳 Starting Docker containers...${NC}"
./vendor/bin/sail up -d

# Wait for database to be ready
echo -e "${YELLOW}⏳ Waiting for database to be ready...${NC}"
sleep 30

# Run migrations and seeders
echo -e "${YELLOW}🗄️ Setting up database...${NC}"
./vendor/bin/sail artisan migrate:fresh --seed

# Install Filament
echo -e "${YELLOW}🎨 Setting up Filament admin panel...${NC}"
./vendor/bin/sail artisan filament:install --panels

# Create admin user
echo -e "${YELLOW}👤 Creating admin user...${NC}"
./vendor/bin/sail artisan make:filament-user

echo -e "${GREEN}✅ Backend setup complete!${NC}"

# Setup Frontend
echo -e "\n${BLUE}🎨 Setting up Vue.js Frontend...${NC}"
cd ../frontend

# Install npm dependencies
echo -e "${YELLOW}📥 Installing npm dependencies...${NC}"
npm install

echo -e "${GREEN}✅ Frontend setup complete!${NC}"

# Final instructions
echo -e "\n${GREEN}🎉 Setup Complete!${NC}"
echo -e "\n${BLUE}📋 Next Steps:${NC}"
echo -e "1. Backend API: ${YELLOW}http://localhost:8000${NC}"
echo -e "2. Admin Panel: ${YELLOW}http://localhost:8000/admin${NC}"
echo -e "3. Start Frontend: ${YELLOW}cd frontend && npm run dev${NC}"
echo -e "4. Frontend URL: ${YELLOW}http://localhost:3000${NC}"
echo -e "\n${BLUE}🔧 Useful Commands:${NC}"
echo -e "• Backend logs: ${YELLOW}cd backend && ./vendor/bin/sail logs -f${NC}"
echo -e "• Stop containers: ${YELLOW}cd backend && ./vendor/bin/sail down${NC}"
echo -e "• Database access: ${YELLOW}cd backend && ./vendor/bin/sail mysql${NC}"
echo -e "\n${BLUE}📚 Documentation:${NC}"
echo -e "• Laravel Sail: ${YELLOW}https://laravel.com/docs/sail${NC}"
echo -e "• Filament: ${YELLOW}https://filamentphp.com/docs${NC}"
echo -e "• Vue.js: ${YELLOW}https://vuejs.org/guide${NC}"

echo -e "\n${GREEN}Happy coding! 🚀${NC}"
