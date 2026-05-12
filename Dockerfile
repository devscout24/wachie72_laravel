FROM php:8.3-cli

# Install system packages and PHP extensions
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    libzip-dev \
    zip \
    nodejs \
    npm \
    && docker-php-ext-install pdo pdo_mysql zip

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy project files
COPY . .

# Create Laravel storage directories
RUN mkdir -p storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache

# Set permissions
RUN chmod -R 775 storage bootstrap/cache

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Install Node dependencies and build assets
RUN npm install && npm run build

# Cache Laravel configuration (ignore failures if APP_KEY is not set yet)
RUN php artisan config:cache || true
RUN php artisan route:cache || true
RUN php artisan view:cache || true

# Render uses port 10000
EXPOSE 10000

# Start Laravel
CMD php artisan serve --host=0.0.0.0 --port=10000