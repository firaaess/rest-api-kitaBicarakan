# Gunakan PHP 8.3 dengan Apache
FROM php:8.3-apache

# Install dependencies
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libpng-dev \
    zip unzip \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory ke Laravel
WORKDIR /var/www/html

# Copy semua file Laravel ke dalam container
COPY . .

# Install dependensi Laravel
RUN composer install --no-dev --optimize-autoloader

# Ubah permission storage dan cache
RUN chmod -R 777 storage bootstrap/cache

# Expose port Laravel
EXPOSE 80

# Jalankan Apache
CMD ["apache2-foreground"]
