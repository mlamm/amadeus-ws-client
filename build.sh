#!/bin/bash
echo -e "\n";
echo -e "==> \033[1;35mGetting new composer...\033[0m";
docker exec -it service-amadeus-php rm -rf composer.phar
docker exec -it service-amadeus-php wget https://getcomposer.org/composer.phar
if [ $1 = "dev" ];
then
    echo -e "==> \033[1;35mRunning 'DEV' build...\033[0m";
    docker exec -it service-amadeus-php ./build/dev.sh
elif [ $1 = "test" ];
then
    echo -e "==> \033[1;35mRunning 'TEST' build...\033[0m";
    docker exec -it service-amadeus-php ./build/test.sh
elif [ $1 = "stage" ];
then
    echo -e "==> \033[1;35mRunning 'STAGE' build...\033[0m";
    docker exec -it service-amadeus-php ./build/stage.sh
elif [ $1 = "prod" ];
then
    echo -e "==> \033[1;35mRunning 'PROD' build...\033[0m";
    docker exec -it service-amadeus-php ./build/prod.sh
else
    echo -e "==> \033[1;31mEnvironment not registered. Stop.\033[0m"
fi

echo -e "==> \033[1;32mdone\033[0m";