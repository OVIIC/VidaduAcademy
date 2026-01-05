#!/bin/bash

# VidaduAcademy Oracle Cloud Setup Script (Docker Version)
# Usage: ./oracle-setup.sh

set -e

echo "🚀 Starting Oracle Cloud Server Setup..."

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

print_status() { echo -e "${GREEN}[INFO]${NC} $1"; }
print_warning() { echo -e "${YELLOW}[WARNING]${NC} $1"; }
print_error() { echo -e "${RED}[ERROR]${NC} $1"; }

# 1. Update System
print_status "Updating system packages..."
if [ -x "$(command -v apt-get)" ]; then
    sudo apt-get update && sudo apt-get upgrade -y
    sudo apt-get install -y git curl ufw
elif [ -x "$(command -v yum)" ]; then
    sudo yum update -y
    sudo yum install -y git curl firewalld
fi

# 2. Install Docker & Docker Compose
print_status "Installing Docker..."
if ! [ -x "$(command -v docker)" ]; then
    curl -fsSL https://get.docker.com -o get-docker.sh
    sudo sh get-docker.sh
    sudo usermod -aG docker $USER
    print_status "Docker installed. You may need to logout and login for group changes to verify."
else
    print_status "Docker already installed."
fi

# 3. Configure Firewall (Ubuntu/UFW or Oracle Linux/Firewalld)
print_status "Configuring Firewall..."

# Try UFW (Ubuntu)
if [ -x "$(command -v ufw)" ]; then
    sudo ufw allow 22/tcp
    sudo ufw allow 80/tcp
    sudo ufw allow 443/tcp
    # Oracle Cloud often uses port 80/443 for ingress, ensure they are open
    echo "y" | sudo ufw enable
fi

# Try Firewalld (Oracle Linux)
if [ -x "$(command -v firewall-cmd)" ]; then
    sudo systemctl start firewalld
    sudo firewall-cmd --permanent --add-service=http
    sudo firewall-cmd --permanent --add-service=https
    sudo firewall-cmd --reload
fi

print_warning "IMPORTANT: You must also manually open ports 80 and 443 in the Oracle Cloud Console 'Security List'."

# 4. Clone Repository
APP_DIR="/var/www/vidaduacademy"
if [ ! -d "$APP_DIR" ]; then
    print_status "Cloning repository..."
    sudo mkdir -p $APP_DIR
    sudo chown $USER:$USER $APP_DIR
    git clone https://github.com/your-repo/vidaduacademy.git $APP_DIR
else
    print_status "Repository exists. Pulling latest..."
    cd $APP_DIR
    git pull origin main
fi

# 5. Environment Setup
cd $APP_DIR
if [ ! -f .env ]; then
    print_warning "Creating .env file from example..."
    cp backend/.env.example .env
    # Modify DB_HOST for docker
    sed -i 's/DB_HOST=127.0.0.1/DB_HOST=db/g' .env
    sed -i 's/REDIS_HOST=127.0.0.1/REDIS_HOST=redis/g' .env
fi

# 6. Start Application
print_status "Starting Application Stack..."
docker compose up -d --build

print_status "🎉 Setup Complete!"
print_status "Your app is running in containers."
print_status "Next Steps:"
print_status "1. Update .env with real database credentials."
print_status "2. Run migrations: docker compose exec app php artisan migrate"
