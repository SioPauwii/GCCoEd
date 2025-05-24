FROM php:8.1-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nodejs \
    npm

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Get specific Composer version
COPY --from=composer:2.5.8 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy only necessary files first
COPY composer.* ./
COPY package*.json ./

# Install dependencies with more permissive settings
RUN composer config --global process-timeout 2000 && \
    composer install \
    --ignore-platform-reqs \
    --no-scripts \
    --no-dev \
    --prefer-dist \
    --no-interaction

# Copy the rest of the application
COPY . .

# Generate autoload files
RUN composer dump-autoload --optimize --no-dev

# Set permissions
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/storage \
    && chmod -R 755 /var/www/bootstrap/cache

EXPOSE 9000

CMD ["php-fpm"]