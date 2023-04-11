# Development/testing only!
FROM php:8.2.3
RUN apt-get -y update && apt-get -y install git && apt-get -y install libpq-dev && docker-php-ext-install pdo_pgsql
COPY --from=composer:2.5.4 /usr/bin/composer /usr/bin/composer
WORKDIR /opt
COPY Slimfony/ /opt/Slimfony
WORKDIR /opt/anubis
COPY anubis/composer.json anubis/composer.lock? /opt/anubis/
RUN composer install --no-interaction
COPY anubis/ /opt/anubis
#WORKDIR /opt/anubis/app
#COPY anubis/ /opt/anubis/app

WORKDIR /opt/anubis/public
CMD ["php", "-S", "anubis:80"]
#CMD ["/bin/bash", "-c", "composer install --no-interaction;cd public;php -S anubis:80"]
#CMD ["composer", "install",  "--no-interaction", "", "cd public", "&", "php", "-S", "anubis:80"]
#CMD ["tail", "-f", "/dev/null"]