#!/bin/bash
PROJECT_COLOR='\033[1;33m'
TASK_COLOR='\033[0m'
NORMAL_COLOR='\033[0m'
PROJECT="${PROJECT_COLOR}[AMADEUS SERVICE]${NORMAL_COLOR}";

echo -e "==> ${PROJECT} -- ${TASK_COLOR}Setting config...${NORMAL_COLOR}";
cp config/dist/dev.yml.dist config/app.yml