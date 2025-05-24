FROM php:8.1-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Get Composer version 2.5.8
COPY --from=composer:2.5.8 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy composer files first
COPY composer.json composer.lock ./

# Install dependencies with modified flags
RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction \
    --no-plugins \
    --prefer-dist

# Copy the rest of the application
COPY . .

# Set permissions
RUN chown -R www-data:www-data /var/www

EXPOSE 9000

CMD ["php-fpm"]