FROM php:8.1-fpm

# Install dockerize
ENV DOCKERIZE_VERSION 0.6.1
RUN curl -s -f -L -o /tmp/dockerize.tar.gz https://github.com/jwilder/dockerize/releases/download/v$DOCKERIZE_VERSION/dockerize-linux-amd64-v$DOCKERIZE_VERSION.tar.gz \
    && tar -C /usr/local/bin -xzvf /tmp/dockerize.tar.gz \
    && rm /tmp/dockerize.tar.gz

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y --no-install-recommends \
        libz-dev \
        iputils-ping \
        libpq-dev \
        libjpeg62-turbo-dev \
        libfreetype-dev \
        libjpeg-dev \
        libpng-dev \
        libssl-dev \
        zlib1g-dev \
        libzip-dev \
        unzip \
        zip \
    && pecl install redis \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-configure zip \
    && docker-php-ext-install -j$(nproc) gd exif opcache pdo_mysql pdo_pgsql pgsql pcntl zip \
    && docker-php-ext-enable redis \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2.7.4 /usr/bin/composer /usr/bin/composer

# Install Node.js
RUN curl -sL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# Create a group and user
RUN groupadd -g 1000 www \
    && useradd -u 1000 -ms /bin/bash -g www www

# Set working directory
WORKDIR /var/www/app

# Copy application code and set ownership to the www user
COPY --chown=www:www . .
# Install Composer dependencies
RUN composer install --no-scripts --no-autoloader \
    && composer dump-autoload --optimize \
    && composer run-script post-install-cmd

# Generate application key and create storage symlink
RUN php artisan key:generate \
    && php artisan storage:link

# Set the correct permissions
RUN chown -R www-data:www-data . \
    && chmod -R 775 storage

# Install Vite and project dependencies
RUN npm i -g vite \
    && npm i \
    && npm run build

USER www

# Expose port 8000 for PHP-FPM
EXPOSE 9000

# Start PHP-FPM
CMD ["php-fpm"]
