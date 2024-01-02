# Use an official PHP image as the base image
FROM php:8.2-fpm

# Set the working directory to /var/www/html
WORKDIR /var/www/html

# Install necessary dependencies
RUN apt-get update && apt-get install -y \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Copy composer.lock and composer.json
COPY composer.lock composer.json /var/www/html/

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install dependencies
RUN composer install --no-scripts --no-autoloader

# Copy the rest of the application code
COPY . /var/www/html

# Generate autoload files
RUN composer dump-autoload --optimize
RUN groupadd -g 1000 www
RUN useradd -u 1000 -ms /bin/bash -g www www
# Set permissions
RUN chown -R www:www /var/www/html/storage /var/www/html/bootstrap/cache

# Configure Nginx
COPY deployment/nginx/default.conf /etc/nginx/sites-available/default

# Copy existing application directory permissions
COPY --chown=www:www . /var/www

# Change current user to www
USER www
