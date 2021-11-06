FROM php:7.4.25-apache
RUN apt-get -y update && apt-get upgrade -y
RUN apt-get update && apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        zip \
        unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install mysqli
# Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
WORKDIR /var/www/html
COPY ./src/composer.json ./src/composer.lock ./ 
RUN composer install --no-scripts 
COPY ./src/ ./ 
RUN a2enmod rewrite && service apache2 restart
