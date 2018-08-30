FROM 630542070554.dkr.ecr.eu-central-1.amazonaws.com/flight/invia/image/amadeus/app/dev:latest AS development

COPY --chown=www-data:www-data  . /var/www

FROM 630542070554.dkr.ecr.eu-central-1.amazonaws.com/flight/invia/image/amadeus/app:latest AS production

COPY --chown=www-data:www-data  . /var/www