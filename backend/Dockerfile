# Use official PHP image with specific version for stability
FROM php:8.1.31-apache

# Set environment variables for build optimization
ENV COMPOSER_PROCESS_TIMEOUT=600
ENV COMPOSER_MEMORY_LIMIT=2G
ENV COMPOSER_CACHE_DIR=/tmp/composer-cache
ENV DEBIAN_FRONTEND=noninteractive

# Set working directory
WORKDIR /var/www/html

# Install system dependencies with network retry logic
RUN for i in 1 2 3; do \
        apt-get update && break || { \
            echo "apt-get update failed, retry $i/3"; \
            sleep 5; \
        } \
    done && \
    for i in 1 2 3; do \
        apt-get install -y --no-install-recommends \
            git \
            curl \
            libpng-dev \
            libonig-dev \
            libxml2-dev \
            zip \
            unzip && break || { \
            echo "apt-get install failed, retry $i/3"; \
            sleep 5; \
        } \
    done && \
    docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*

# Install Composer with version lock
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Configure Apache DocumentRoot
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

# Copy composer files first (better caching)
COPY composer.json composer.lock ./

# Create .env for build process
COPY .env.build /var/www/html/.env

# Install PHP dependencies with enhanced network optimizations and retry logic
RUN composer config --global process-timeout 600 && \
    composer config --global cache-dir /tmp/composer-cache && \
    composer config --global repos.packagist composer https://packagist.org && \
    composer config --global github-protocols https ssh && \
    composer config --global preferred-install dist && \
    for i in 1 2 3; do \
        composer install --no-dev --optimize-autoloader --no-interaction \
        --ignore-platform-reqs --no-scripts --prefer-dist && break || { \
            echo "Composer install failed, retry $i/3"; \
            composer clear-cache; \
            sleep 10; \
        } \
    done && \
    composer clear-cache

# Copy rest of application
COPY . /var/www/html

# Run composer scripts after copying source
RUN composer dump-autoload --optimize

# Remove build .env (Railway will set proper env vars)
RUN rm -f /var/www/html/.env

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Create startup script that creates .env from environment variables
RUN echo '#!/bin/bash\n\
set -e\n\
\n\
# Network connectivity check\n\
echo "Checking network connectivity..."\n\
for i in {1..5}; do\n\
    if ping -c 1 8.8.8.8 >/dev/null 2>&1; then\n\
        echo "Network connectivity: OK"\n\
        break\n\
    else\n\
        echo "Network check $i/5 failed, retrying..."\n\
        sleep 2\n\
    fi\n\
done\n\
\n\
# Create .env from Railway environment variables\n\
echo "Creating .env file from environment variables..."\n\
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
# Wait for database to be ready\n\
echo "Waiting for database connection..."\n\
for i in {1..30}; do\n\
    if php artisan migrate:status >/dev/null 2>&1; then\n\
        echo "Database connection: OK"\n\
        break\n\
    else\n\
        echo "Database not ready, waiting... ($i/30)"\n\
        sleep 2\n\
    fi\n\
done\n\
\n\
# Run Laravel setup with error handling\n\
echo "Running Laravel setup..."\n\
php artisan config:cache || echo "Config cache failed, continuing..."\n\
php artisan route:cache || echo "Route cache failed, continuing..."\n\
php artisan view:cache || echo "View cache failed, continuing..."\n\
php artisan migrate --force || echo "Migration failed, continuing..."\n\
\n\
echo "Starting Apache server..."\n\
# Start Apache\n\
apache2-foreground' > /start.sh && chmod +x /start.sh

EXPOSE 80

CMD ["/start.sh"]
