#!/bin/bash

# VidaduAcademy Production Deploy Script
# Usage: ./deploy.sh

set -e

echo "🚀 Starting VidaduAcademy deployment..."

# Colors for output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Configuration
BACKEND_PATH="/var/www/vidaduacademy/backend"
FRONTEND_PATH="/var/www/vidaduacademy/frontend"
DOMAIN="vidaduacademy.sk"

# Function to print colored output
print_status() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Check if running as root
if [[ $EUID -eq 0 ]]; then
   print_error "This script should not be run as root"
   exit 1
fi

# 1. Update system packages
print_status "Updating system packages..."
sudo apt update && sudo apt upgrade -y

# 2. Install required packages
print_status "Installing required packages..."
sudo apt install -y nginx mysql-server redis-server php8.2-fpm php8.2-mysql php8.2-redis php8.2-curl php8.2-gd php8.2-mbstring php8.2-xml php8.2-zip php8.2-bcmath php8.2-intl certbot python3-certbot-nginx nodejs npm composer git unzip

# 3. Clone or update repository
if [ -d "/var/www/vidaduacademy" ]; then
    print_status "Updating existing repository..."
    cd /var/www/vidaduacademy
    git pull origin main
else
    print_status "Cloning repository..."
    sudo mkdir -p /var/www/vidaduacademy
    sudo chown $USER:$USER /var/www/vidaduacademy
    git clone https://github.com/yourusername/vidaduacademy.git /var/www/vidaduacademy
fi

# 4. Backend deployment
print_status "Deploying backend..."
cd $BACKEND_PATH

# Install PHP dependencies
composer install --no-dev --optimize-autoloader

# Set up environment file
if [ ! -f .env ]; then
    cp .env.production.example .env
    print_warning "Please configure .env file with your production settings"
    read -p "Press enter to continue after configuring .env..."
fi

# Generate application key if not set
php artisan key:generate --force

# Run database migrations
php artisan migrate --force

# Seed database (only on first deploy)
read -p "Run database seeding? (y/N): " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    php artisan db:seed --force
fi

# Cache optimization
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set proper permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

# 5. Frontend deployment
print_status "Deploying frontend..."
cd $FRONTEND_PATH

# Install Node.js dependencies
npm ci

# Set up environment file
if [ ! -f .env.production ]; then
    cp .env.production.example .env.production
    print_warning "Please configure .env.production file with your production settings"
    read -p "Press enter to continue after configuring .env.production..."
fi

# Build for production
npm run build

# 6. Configure Nginx
print_status "Configuring Nginx..."

# Backend API configuration
sudo tee /etc/nginx/sites-available/api.$DOMAIN << EOF
server {
    listen 80;
    server_name api.$DOMAIN;
    root $BACKEND_PATH/public;
    index index.php;

    client_max_body_size 100M;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny all;
    }
}
EOF

# Frontend configuration
sudo tee /etc/nginx/sites-available/app.$DOMAIN << EOF
server {
    listen 80;
    server_name app.$DOMAIN;
    root $FRONTEND_PATH/dist;
    index index.html;

    # Gzip compression
    gzip on;
    gzip_types text/plain text/css application/json application/javascript text/xml application/xml application/xml+rss text/javascript;

    location / {
        try_files \$uri \$uri/ /index.html;
    }

    # Cache static assets
    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}
EOF

# Enable sites
sudo ln -sf /etc/nginx/sites-available/api.$DOMAIN /etc/nginx/sites-enabled/
sudo ln -sf /etc/nginx/sites-available/app.$DOMAIN /etc/nginx/sites-enabled/

# Remove default site
sudo rm -f /etc/nginx/sites-enabled/default

# Test Nginx configuration
sudo nginx -t

# Reload Nginx
sudo systemctl reload nginx

# 7. SSL certificates
print_status "Setting up SSL certificates..."
sudo certbot --nginx -d api.$DOMAIN -d app.$DOMAIN --non-interactive --agree-tos -m admin@$DOMAIN

# 8. Configure systemd services
print_status "Configuring services..."

# Laravel queue worker
sudo tee /etc/systemd/system/vidaduacademy-worker.service << EOF
[Unit]
Description=VidaduAcademy Queue Worker
After=network.target

[Service]
Type=simple
User=www-data
WorkingDirectory=$BACKEND_PATH
ExecStart=/usr/bin/php artisan queue:work --verbose --tries=3 --timeout=90
Restart=always
RestartSec=5

[Install]
WantedBy=multi-user.target
EOF

# Laravel scheduler
sudo tee /etc/systemd/system/vidaduacademy-scheduler.service << EOF
[Unit]
Description=VidaduAcademy Scheduler
After=network.target

[Service]
Type=simple
User=www-data
WorkingDirectory=$BACKEND_PATH
ExecStart=/usr/bin/php artisan schedule:run
Restart=always
RestartSec=60

[Install]
WantedBy=multi-user.target
EOF

# Enable and start services
sudo systemctl enable vidaduacademy-worker.service
sudo systemctl enable vidaduacademy-scheduler.service
sudo systemctl start vidaduacademy-worker.service
sudo systemctl start vidaduacademy-scheduler.service

# 9. Setup cron for Laravel scheduler
print_status "Setting up cron job..."
(crontab -l 2>/dev/null; echo "* * * * * cd $BACKEND_PATH && php artisan schedule:run >> /dev/null 2>&1") | crontab -

# 10. Final checks
print_status "Performing final checks..."

# Check if services are running
if systemctl is-active --quiet nginx; then
    print_status "✅ Nginx is running"
else
    print_error "❌ Nginx is not running"
fi

if systemctl is-active --quiet mysql; then
    print_status "✅ MySQL is running"
else
    print_error "❌ MySQL is not running"
fi

if systemctl is-active --quiet redis; then
    print_status "✅ Redis is running"
else
    print_error "❌ Redis is not running"
fi

# 11. Security hardening
print_status "Applying security hardening..."

# Hide Nginx version
sudo sed -i 's/# server_tokens off;/server_tokens off;/g' /etc/nginx/nginx.conf

# Configure firewall
sudo ufw allow ssh
sudo ufw allow 'Nginx Full'
sudo ufw --force enable

print_status "🎉 Deployment completed successfully!"
print_status "Your application should be available at:"
print_status "  Frontend: https://app.$DOMAIN"
print_status "  API: https://api.$DOMAIN"
print_status ""
print_warning "Don't forget to:"
print_warning "  1. Configure your production database"
print_warning "  2. Set up Stripe webhooks to: https://api.$DOMAIN/api/stripe/webhook"
print_warning "  3. Configure your email service (Mailgun, SendGrid, etc.)"
print_warning "  4. Set up regular database backups"
print_warning "  5. Configure monitoring and logging"
