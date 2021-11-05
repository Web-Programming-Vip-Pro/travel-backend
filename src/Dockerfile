FROM php:7.4-apache
WORKDIR /var/www/html
COPY . .
RUN a2enmod rewrite && service apache2 restart
RUN docker-php-ext-install mysqli