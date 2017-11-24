FROM php:7.1-fpm-alpine

ENV XDEBUG_VERSION=2.5.5

# Install packages we want to keep
RUN apk --no-cache add fcgi git openssh libxml2-dev libxslt-dev zlib-dev libbz2 openssl-dev

# Install dependencies that are only used during installation of other packages. Afterwards it will be cleaned up.
RUN set -xe \
    && apk add --no-cache --virtual .build-deps \
       $PHPIZE_DEPS \
       coreutils \
       bzip2-dev \
    && docker-php-ext-install -j"$(/usr/bin/nproc)" mbstring zip soap pdo pdo_mysql mysqli xsl bz2 \
    && pecl install xdebug-$XDEBUG_VERSION mongodb \
    && docker-php-ext-enable xdebug mbstring zip soap pdo pdo_mysql mysqli xsl mongodb \
    && apk del .build-deps

# install tideways extension
ENV TIDEWAYS_VERSION=4.1.4
RUN wget -Otideways-php.tar.gz "https://s3-eu-west-1.amazonaws.com/tideways/extension/4.1.4/tideways-php-$TIDEWAYS_VERSION-x86_64.tar.gz" \
    && tar xzvf tideways-php.tar.gz \
    && cd tideways-php-$TIDEWAYS_VERSION \
    && sh install.sh \
    && echo "extension=tideways.so" > $PHP_INI_DIR/conf.d/tideways.ini \
    && echo "tideways.auto_prepend_library=0" >> $PHP_INI_DIR/conf.d/tideways.ini \
    && rm -rf /tmp/tideways-php*

# Use our own xdebug configuration.
# Just extend the existing xdebug config as the path to xdebug.so is already existent.
COPY ./scripts/docker/php/20-xdebug.ini /tmp/xdebug.ini
RUN cat /tmp/xdebug.ini >> $PHP_INI_DIR/conf.d/docker-php-ext-xdebug.ini

COPY . /var/www
WORKDIR /var/www
