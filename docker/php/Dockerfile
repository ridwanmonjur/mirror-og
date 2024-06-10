FROM php:8.1-fpm

# Install dockerize so we can wait for containers to be ready
ENV DOCKERIZE_VERSION 0.6.1

RUN curl -s -f -L -o /tmp/dockerize.tar.gz https://github.com/jwilder/dockerize/releases/download/v$DOCKERIZE_VERSION/dockerize-linux-amd64-v$DOCKERIZE_VERSION.tar.gz \
    && tar -C /usr/local/bin -xzvf /tmp/dockerize.tar.gz \
    && rm /tmp/dockerize.tar.gz

# Install Composer
ENV COMPOSER_VERSION 2.7.4

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer --version=$COMPOSER_VERSION



ENV PATH $NVM_DIR/versions/node/0.39.7/bin:$PATH

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        libz-dev \
        libpq-dev \
        libjpeg-dev \
        libpng-dev \
        libssl-dev \
        libzip-dev \
        unzip \
        zip \
    && apt-get clean \
    && pecl install redis \
    && docker-php-ext-configure gd \
    && docker-php-ext-configure zip \
    && docker-php-ext-install \
        gd \
        exif \
        opcache \
        pdo_mysql \
        pdo_pgsql \
        pgsql \
        pcntl \
        zip \
    && docker-php-ext-enable redis \
    && rm -rf /var/lib/apt/lists/*;

COPY ./docker/php/laravel.ini /usr/local/etc/php/conf.d/laravel.ini
ENV COMPOSER_ALLOW_SUPERUSER=1

WORKDIR /usr/src/app


COPY --from=composer /usr/bin/composer /usr/bin/composer
COPY ./composer.json .
COPY ./composer.lock .
COPY ./package.json .
COPY ./package-lock.json .

# process project code
# Install nvm, Node.js, and npm

ENV NVM_DIR /usr/local/nvm
ENV NODE_VERSION 20.14.0

RUN cd /usr/local && mkdir nvm

RUN curl -sL https://deb.nodesource.com/setup_20.x | bash - 
RUN apt-get install -y nodejs

ENV PATH $NVM_DIR/versions/node/$NODE_VERSION/bin:$PATH

RUN echo "Node: " && node -v
RUN echo "NPM: " && npm -v
COPY . .
RUN composer install --no-scripts --no-autoloader
RUN composer dump-autoload --optimize &&\
    composer run-script post-install-cmd &&\
    php artisan key:generate &&\
    php artisan storage:link
RUN npm i -g vite
RUN npm i
RUN npm run build

RUN groupadd -g 1000 www
RUN useradd -u 1000 -ms /bin/bash -g www www

COPY . /var/www

COPY --chown=www:www . /var/www

USER www

EXPOSE 9000