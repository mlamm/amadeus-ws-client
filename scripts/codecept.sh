#!/bin/bash

docker-compose exec -T \
  amadeus-php \
  /var/www/vendor/bin/codecept run -v --steps --no-interaction
