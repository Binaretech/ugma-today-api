FROM php:apache

ENV APACHE_DOCUMENT_ROOT /app/public

RUN apt-get update -y

RUN apt-get install -y libpq-dev curl libxml2-dev git nano openssl

RUN docker-php-ext-install bcmath mbstring pdo_pgsql xml pgsql intl
RUN pecl install xdebug
RUN docker-php-ext-enable xdebug

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

RUN a2enmod rewrite headers
