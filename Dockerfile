FROM php:7.4-apache

ENV APACHE_DOCUMENT_ROOT /app/public
COPY . /app

RUN apt-get update -y && apt-get install -y libpq-dev libxml2-dev libzip-dev curl libonig-dev && rm -r /var/lib/apt/lists/*
RUN docker-php-ext-install bcmath mbstring pdo_pgsql xml pgsql intl zip

WORKDIR /app

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

EXPOSE 80

RUN a2enmod rewrite headers
