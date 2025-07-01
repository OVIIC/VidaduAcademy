# Use official PHP image with specific version for stability
FROM php:8.3-apache

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
            libpq-dev \
            zip \
            unzip && break || { \
            echo "apt-get install failed, retry $i/3"; \
            sleep 5; \
        } \
    done && \
    docker-php-ext-install pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd && \
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

# Set permissions first
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache \
    && mkdir -p /var/www/html/storage/logs \
    && mkdir -p /var/www/html/storage/framework/cache \
    && mkdir -p /var/www/html/storage/framework/sessions \
    && mkdir -p /var/www/html/storage/framework/views \
    && touch /var/www/html/storage/logs/laravel.log \
    && chmod 666 /var/www/html/storage/logs/laravel.log

# Run composer scripts after setting permissions
RUN composer dump-autoload --optimize || echo "Autoload dump failed, continuing..."

# Remove build .env (start-render.sh will create proper one)
RUN rm -f /var/www/html/.env

# Make start script executable (already copied by COPY . /var/www/html)
RUN chmod +x /var/www/html/start-render.sh

# Expose port (Render will set PORT environment variable)  
EXPOSE ${PORT:-80}

# Use our optimized start script
CMD ["/var/www/html/start-render.sh"]
