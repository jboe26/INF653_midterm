# Use the official PHP image with Apache
FROM php:7.4-apache

# Set environment variables to streamline installation
ENV ACCEPT_EULA=Y
ENV DEBIAN_FRONTEND=noninteractive

# Install required system packages and PHP extensions
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install mysqli pdo pdo_mysql pdo_pgsql

# Enable Apache mod_rewrite for clean URLs
RUN a2enmod rewrite

# Copy the application code into the container
COPY . /var/www/html/

# Set the working directory in the container
WORKDIR /var/www/html/

# Ensure proper permissions for Apache
RUN chown -R www-data:www-data /var/www/html/
RUN chmod -R 755 /var/www/html/

# Expose port 80 for HTTP traffic
EXPOSE 80

# Start Apache server
CMD ["apache2-foreground"]
