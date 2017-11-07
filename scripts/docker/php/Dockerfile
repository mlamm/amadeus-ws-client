FROM php:7.1-fpm-alpine

ENV XDEBUG_VERSION=2.5.5

# Install packages we want to keep
RUN apk --no-cache add fcgi git openssh libxml2-dev libxslt-dev zlib-dev

# Install dependcies that are only used during installation of other packages. Afterwards it will be cleaned up.
RUN set -xe \
    && apk add --no-cache --virtual .build-deps \
       $PHPIZE_DEPS \
       coreutils \
    && docker-php-ext-install -j"$(/usr/bin/nproc)" mbstring zip soap pdo pdo_mysql mysqli xsl \
    && pecl install xdebug-$XDEBUG_VERSION \
    && docker-php-ext-enable xdebug mbstring zip soap pdo pdo_mysql mysqli xsl \
    && apk del .build-deps

# Use our own xdebug configuration.
# Just extend the existing xdebug config as the path to xdebug.so is already existent.
COPY ./scripts/docker/php/20-xdebug.ini /tmp/xdebug.ini
RUN cat /tmp/xdebug.ini >> $PHP_INI_DIR/conf.d/docker-php-ext-xdebug.ini

COPY . /var/www
WORKDIR /var/www
