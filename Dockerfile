FROM php:apache

ENV APACHE_DOCUMENT_ROOT /app/public

RUN apt-get update -y
RUN apt-get install -y libpq-dev libxml2-dev curl libonig-dev

RUN docker-php-ext-install bcmath mbstring pdo_pgsql xml pgsql intl
# RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

RUN a2enmod rewrite headers
