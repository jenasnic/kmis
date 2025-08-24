FROM composer:2 AS composer
FROM php:8.3-fpm AS php

# Add PHP extensions
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions fileinfo gd intl mbstring pdo_mysql zip

# Set timezone
RUN echo 'Europe/Paris' > /etc/timezone

# Add git
RUN apt update
RUN apt install -y git

COPY --from=composer /usr/bin/composer /usr/bin/composer

# Create composer cache folder
RUN mkdir -p /.composer/cache
RUN chmod -R 777 /.composer/cache

WORKDIR /www
