# Development/testing only!
FROM php:8.2.3
RUN apt-get -y update && apt-get -y install git
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
#CMD ["/bin/bash", "-c", "composer install --no-interaction;cd public;php -S slimfony-test:80"]
#CMD ["composer", "install",  "--no-interaction", "", "cd public", "&", "php", "-S", "slimfony-test:80"]
#CMD ["tail", "-f", "/dev/null"]