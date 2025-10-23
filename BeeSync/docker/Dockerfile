FROM node:20-slim as node

WORKDIR /app
COPY package*.json ./
RUN npm ci
COPY . .
RUN npm run build

FROM php:8.3-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    netcat-traditional

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy composer files
COPY composer.* ./

# Install composer dependencies
RUN composer install --no-scripts --no-dev --prefer-dist

# Copy application files
COPY . .
COPY --from=node /app/public/build public/build
COPY .env.example .env

# Copy and configure startup script
COPY docker/start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

# Clear all caches
RUN rm -rf bootstrap/cache/*
RUN rm -rf storage/framework/cache/*
RUN rm -rf storage/framework/views/*
RUN rm -rf storage/framework/sessions/*

# Create cache directories if they don't exist
RUN mkdir -p \
    storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache

# Set permissions
RUN chown -R www-data:www-data \
    storage \
    bootstrap/cache \
    public/build
RUN chmod -R 775 \
    storage \
    bootstrap/cache \
    public/build

# Define startup script
CMD ["/usr/local/bin/start.sh"]
