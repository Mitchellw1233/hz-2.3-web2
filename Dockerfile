# Development/testing only!
FROM php:8.2.3
RUN apt-get update
COPY --from=composer:2.5.4 /usr/bin/composer /usr/bin/composer
WORKDIR /opt
COPY Slimfony/ /opt/Slimfony
WORKDIR /opt/slimfonytest
COPY slimfonytest/composer.json slimfonytest/composer.lock? /opt/slimfonytest/
RUN composer install --no-interaction
COPY slimfonytest/ /opt/slimfonytest
#WORKDIR /opt/slimfonytest/app
#COPY slimfonytest/ /opt/slimfonytest/app

WORKDIR /opt/slimfonytest/public
CMD ["php", "-S", "slimfony-test:80"]
