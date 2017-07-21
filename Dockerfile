# Add real content for a production ready Dockerfile
FROM tsari/jessie-php-fpm-xdebug

ADD . /var/www
WORKDIR /var/www

RUN php composer.phar update -o
RUN chmod -R 777 ./var