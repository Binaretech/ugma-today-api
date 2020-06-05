FROM php:7.4

RUN apt-get update -y && apt-get install -y libpq-dev libxml2-dev curl libonig-dev git libzip-dev && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install bcmath mbstring pdo_pgsql xml pgsql intl zip

RUN curl -sS https://getcomposer.org/installer | php