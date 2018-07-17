FROM php:7.2.7-fpm-alpine

# Install packages we want to keep
RUN apk --no-cache add fcgi git openssh libxml2 libxslt zlib libbz2 openssl-dev

# Install dependencies that are only used during installation of other packages. Afterwards it will be cleaned up.
RUN set -xe \
    && apk add --no-cache --virtual .build-deps \
       $PHPIZE_DEPS \
       coreutils \
       bzip2-dev libxml2-dev libxslt-dev zlib-dev \
    && docker-php-ext-install -j"$(/usr/bin/nproc)" zip soap pdo_mysql xsl bz2 \
    && pecl install mongodb redis apcu \
    && docker-php-ext-enable opcache mongodb redis apcu \
    && apk del .build-deps

# install tideways extension
ENV TIDEWAYS_VERSION=4.1.6
RUN wget -Otideways-php.tar.gz "https://s3-eu-west-1.amazonaws.com/tideways/extension/$TIDEWAYS_VERSION/tideways-php-$TIDEWAYS_VERSION-x86_64.tar.gz" \
    && tar xzvf tideways-php.tar.gz \
    && cd tideways-php-$TIDEWAYS_VERSION \
    && sh install.sh \
    && echo "extension=tideways.so" > $PHP_INI_DIR/conf.d/tideways.ini \
    && echo "tideways.auto_prepend_library=0" >> $PHP_INI_DIR/conf.d/tideways.ini \
    && rm -rf /tmp/tideways-php*

COPY ./scripts/docker/php/base/zz-docker.conf /usr/local/etc/php-fpm.d/zz-docker.conf

# Add nginx user for unix socket permission
RUN addgroup -S nginx \
  && adduser -S -D -G nginx nginx

# Change group and allow write to the log directory
RUN mkdir -p /var/www/var/logs /var/www/var/cache \
    && chmod -R 774 /var/www/var/logs \
    && chmod -R 774 /var/www/var/cache \
    && chgrp -R www-data /var/www

COPY . /var/www
WORKDIR /var/www

COPY ./scripts/docker/php/base/docker-entrypoint /usr/local/bin/
ENTRYPOINT ["docker-entrypoint"]

CMD ["php-fpm"]