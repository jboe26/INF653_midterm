# Use the official PHP image with Apache
FROM php:8.1-apache

# Install system dependencies and PostgreSQL PDO extension
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Enable Apache mod_rewrite (optional, helpful for clean URLs or API routing)
RUN a2enmod rewrite

# Copy your project files into the container
COPY . /var/www/html/

# Set file permissions for Apache to access your project
RUN chmod -R 755 /var/www/html && chown -R www-data:www-data /var/www/html

# Set the working directory
WORKDIR /var/www/html/

# Expose port 80 to make the app accessible
EXPOSE 80
