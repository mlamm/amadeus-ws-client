FROM php:7.2.6-fpm-alpine

ENV XDEBUG_VERSION=2.6.0

COPY ./scripts/docker/php/base/zz-docker.conf /usr/local/etc/php-fpm.d/zz-docker.conf

# Add nginx user for unix socket permission
RUN addgroup -S nginx \
  && adduser -S -D -G nginx nginx

# Install packages we want to keep
RUN apk --no-cache add fcgi git openssh libxml2 libxslt zlib libbz2 openssl-dev

# Install dependencies that are only used during installation of other packages. Afterwards it will be cleaned up.
RUN set -xe \
    && apk add --no-cache --virtual .build-deps \
       $PHPIZE_DEPS \
       coreutils \
       bzip2-dev libxml2-dev libxslt-dev zlib-dev \
    && docker-php-ext-install -j"$(/usr/bin/nproc)" zip soap pdo_mysql xsl bz2 \
    && pecl install mongodb redis \
    && pecl install xdebug-$XDEBUG_VERSION \
    && docker-php-ext-enable xdebug \
    && apk del .build-deps

# Use our own xdebug configuration.
# Just extend the existing xdebug config as the path to xdebug.so is already existent.
COPY ./scripts/docker/php/dev/20-xdebug.ini /tmp/xdebug.ini
RUN echo "" >> $PHP_INI_DIR/conf.d/docker-php-ext-xdebug.ini
RUN cat /tmp/xdebug.ini >> $PHP_INI_DIR/conf.d/docker-php-ext-xdebug.ini

COPY ./scripts/docker/php/dev/docker-entrypoint /usr/local/bin/
ENTRYPOINT ["docker-entrypoint"]

CMD ["php-fpm"]
