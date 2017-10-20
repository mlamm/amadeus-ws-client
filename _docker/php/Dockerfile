# Placeholder for the real Dockerfile
FROM tsari/jessie-php-fpm-xdebug
VOLUME /var/www
WORKDIR /var/www

RUN apt-get update \
    && apt-get install -y \
        php7.1-soap \
        php7.1-mysql \
        php7.1-redis \
    && apt-get clean
