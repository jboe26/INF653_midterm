# Use the official PHP image as the base
FROM php:7.4-apache

# Set environment variables to streamline installations
ENV ACCEPT_EULA=Y
ENV DEBIAN_FRONTEND=noninteractive

# Update and install system packages and PHP extensions
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

# Copy the current directory contents into the container's web root
COPY . /var/www/html/

# Set the working directory
WORKDIR /var/www/html/

# Ensure Apache user can access all files
RUN chown -R www-data:www-data /var/www/html/ && chmod -R 755 /var/www/html/

# Expose port 80 for HTTP traffic
EXPOSE 80

# Start Apache server in the foreground
CMD ["apache2-foreground"]
