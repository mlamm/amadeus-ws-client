#!/bin/bash
PROJECT_COLOR='\033[1;33m'
TASK_COLOR='\033[0m'
NORMAL_COLOR='\033[0m'
PROJECT="${PROJECT_COLOR}[AMADEUS SERVICE]${NORMAL_COLOR}";
COMPOSER_VERSION=1.5.1

function build () {
    echo -e "==> ${PROJECT} -- ${TASK_COLOR}Building docker images...${NORMAL_COLOR}";
    buildImages
    echo -e "==> ${PROJECT} -- ${TASK_COLOR}Creating binaries...${NORMAL_COLOR}";
    createBinaries
    echo -e "==> ${PROJECT} -- ${TASK_COLOR}Preparing directories...${NORMAL_COLOR}";
    prepareDirectories
    echo -e "==> ${PROJECT} -- ${TASK_COLOR}Setting config...${NORMAL_COLOR}";
    cp config/dist/dev.yml.dist config/app.yml
    echo -e "==> ${PROJECT} -- ${TASK_COLOR}Running composer install...${NORMAL_COLOR}";
    bin/composer install
    echo -e "==> ${PROJECT} -- ${TASK_COLOR}Create docs...${NORMAL_COLOR}";
    bin/create-docs
}

function buildImages(){
    # nginx
    docker build -t flight/service.amadeus.nginx -f _docker/nginx/Dockerfile ./_docker/nginx

    DOCKER_IMAGE=flight/service.amadeus.php
    docker build -t "$DOCKER_IMAGE" -f _docker/php/Dockerfile ./_docker/php
}

function createBinaries(){
    if [ ! -d ./bin ]; then
        mkdir ./bin
    fi

    # build helper scripts
    echo -e '#!/bin/sh\n\ndocker run --rm -it -v $(pwd):/var/www -w /var/www -u $(id -u):$(id -g) christianbladescb/aglio -i ./docs/api.apib -o ./var/docs/index.html --theme-variables flatly --theme-full-width' > bin/create-docs
    echo -e '#!/bin/sh\n\nsudo docker exec -ti -u "$(id -u):$(id -g)" service-amadeus-php vendor/bin/codecept "$@" > bin/codecept
    echo -e '#!/bin/sh\n\ndocker run -it --rm -e "HOME=/home/$USER" -e "USER=$USER" -e "UID=$(id -u)" -e "GID=$(id -g)" -v $HOME:/home/$USER -v $PWD:/app -w /app --net="host" '$DOCKER_IMAGE' php bin/composer.phar "$@"' > bin/composer

    if [ ! -f bin/composer.phar ]; then
        php -r "copy('https://getcomposer.org/download/$COMPOSER_VERSION/composer.phar', 'bin/composer.phar');"
    fi

    chmod +x -R ./bin
}

function prepareDirectories(){
    chmod -R 777 ./var
    chmod -R 777 ./tests/_output
}

build
