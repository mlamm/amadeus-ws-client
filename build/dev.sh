#!/bin/bash
PROJECT_COLOR='\033[1;33m'
TASK_COLOR='\033[0m'
NORMAL_COLOR='\033[0m'
PROJECT="${PROJECT_COLOR}[AMADEUS SERVICE]${NORMAL_COLOR}";

echo -e "==> ${PROJECT} -- ${TASK_COLOR}Setting config...${NORMAL_COLOR}";
docker exec -it service-amadeus-php  cp config/dist/dev.yml.dist config/app.yml
echo -e "==> ${PROJECT} -- ${TASK_COLOR}Running composer install...${NORMAL_COLOR}";
docker exec -it service-amadeus-php php composer.phar install -o
echo -e "==> ${PROJECT} -- ${TASK_COLOR}Running npm install...${NORMAL_COLOR}";
docker run -it node:8-alpine npm install
echo -e "==> ${PROJECT} -- ${TASK_COLOR}Create docs...${NORMAL_COLOR}";
bin/aglio