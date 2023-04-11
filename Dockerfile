# Use the official PHP 8.1 image as the base image
FROM php:8.1.17-fpm

# Install necessary packages and extensions
RUN apt-get update && \
    apt-get install -y \
        zip \
        unzip \
        libpq-dev \
        && docker-php-ext-install \
        pdo \
        pdo_pgsql

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set the working directory
WORKDIR /var/www/html

# Copy the application files
COPY . /var/www/html

# Install the application dependencies
RUN composer install

# Expose the default PHP-FPM port
EXPOSE 9000