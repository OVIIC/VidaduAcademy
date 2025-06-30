# Use official PHP image
FROM php:8.1-apache

# Set working directory
WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2.0 /usr/bin/composer /usr/bin/composer

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Configure Apache DocumentRoot
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

# Copy application files
COPY . /var/www/html

# Create .env for build process
COPY .env.build /var/www/html/.env

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction --ignore-platform-reqs

# Remove build .env (Railway will set proper env vars)
RUN rm -f /var/www/html/.env

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Create startup script that creates .env from environment variables
RUN echo '#!/bin/bash\n\
# Create .env from Railway environment variables\n\
echo "APP_NAME=${APP_NAME:-VidaduAcademy}" > /var/www/html/.env\n\
echo "APP_ENV=${APP_ENV:-production}" >> /var/www/html/.env\n\
echo "APP_KEY=${APP_KEY}" >> /var/www/html/.env\n\
echo "APP_DEBUG=${APP_DEBUG:-false}" >> /var/www/html/.env\n\
echo "APP_URL=${APP_URL:-http://localhost}" >> /var/www/html/.env\n\
echo "LOG_CHANNEL=${LOG_CHANNEL:-stack}" >> /var/www/html/.env\n\
echo "LOG_LEVEL=${LOG_LEVEL:-error}" >> /var/www/html/.env\n\
echo "DB_CONNECTION=${DB_CONNECTION:-mysql}" >> /var/www/html/.env\n\
echo "DATABASE_URL=${DATABASE_URL}" >> /var/www/html/.env\n\
echo "CACHE_DRIVER=${CACHE_DRIVER:-file}" >> /var/www/html/.env\n\
echo "SESSION_DRIVER=${SESSION_DRIVER:-file}" >> /var/www/html/.env\n\
echo "QUEUE_CONNECTION=${QUEUE_CONNECTION:-sync}" >> /var/www/html/.env\n\
\n\
# Run Laravel setup\n\
php artisan config:cache || echo "Config cache failed"\n\
php artisan route:cache || echo "Route cache failed"\n\
php artisan view:cache || echo "View cache failed"\n\
php artisan migrate --force || echo "Migration failed"\n\
\n\
# Start Apache\n\
apache2-foreground' > /start.sh && chmod +x /start.sh

EXPOSE 80

CMD ["/start.sh"]
