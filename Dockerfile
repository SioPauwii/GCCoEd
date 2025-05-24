FROM php:8.1-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Enable Apache modules
RUN a2enmod rewrite

# Get Composer
COPY --from=composer:2.5.8 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy composer files
COPY composer.* ./

# Install dependencies
RUN composer install \
    --no-dev \
    --no-scripts \
    --prefer-dist \
    --no-interaction

# Copy application
COPY . .

# Copy Apache config
COPY docker/000-default.conf /etc/apache2/sites-available/000-default.conf

# Enable site
RUN a2ensite 000-default.conf

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 storage bootstrap/cache

EXPOSE 80

# Use default Apache entrypoint and start Apache
ENTRYPOINT ["docker-php-entrypoint"]
CMD ["apache2-foreground"]