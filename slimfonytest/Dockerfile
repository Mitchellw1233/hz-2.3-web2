# Development/testing only!
FROM php:8.2.3
RUN apt-get update
COPY --from=composer:2.5.4 /usr/bin/composer /usr/bin/composer
WORKDIR /opt
COPY composer.json composer.lock? /opt/
RUN composer install --no-interaction
WORKDIR /opt/app
COPY . /opt/app

WORKDIR /opt/app/public
CMD ["php", "-S", "slimfony-test:80"]
