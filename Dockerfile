# Use the official PHP image with Apache
FROM php:8.1-apache

# Install required PHP extensions, including PDO for PostgreSQL
RUN docker-php-ext-install pdo pdo_pgsql

# Copy your project files into the container
COPY . /var/www/html/

# Set the working directory
WORKDIR /var/www/html/

# Expose port 80 to make the app accessible
EXPOSE 80
