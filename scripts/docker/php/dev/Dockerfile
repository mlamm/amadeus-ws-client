FROM amadeus-php-base-build

ENV XDEBUG_VERSION=2.6.0

# Install dependencies that are only used during installation of other packages. Afterwards it will be cleaned up.
RUN set -xe \
    && apk add --no-cache --virtual .build-deps \
       $PHPIZE_DEPS \
    && pecl install xdebug-$XDEBUG_VERSION \
    && docker-php-ext-enable xdebug \
    && apk del .build-deps

# Use our own xdebug configuration.
# Just extend the existing xdebug config as the path to xdebug.so is already existent.
COPY ./scripts/docker/php/dev/20-xdebug.ini /tmp/xdebug.ini
RUN cat /tmp/xdebug.ini >> $PHP_INI_DIR/conf.d/docker-php-ext-xdebug.ini
