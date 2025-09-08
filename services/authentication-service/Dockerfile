# Start with a PHP 8.2 base image
FROM php:8.2-cli

# Install system dependencies needed for Laravel
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libpq-dev \
    && docker-php-ext-install zip pdo pdo_mysql pdo_pgsql

# Set the working directory
WORKDIR /var/www/html

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy composer files and install dependencies
COPY composer.json composer.lock ./
RUN composer install --no-scripts --no-autoloader

# Copy the rest of the application code
COPY . .

# Finish composer installation
RUN composer dump-autoload --optimize

# Expose the port Laravel will run on
EXPOSE 8000

# The command to start the server
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]