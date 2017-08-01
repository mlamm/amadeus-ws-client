#!/bin/bash
echo -e "\n";
if [ $1 = "dev" ];
then
    echo -e "==> \033[1;35mrunning 'DEV' build...\033[0m";
    ./build/dev.sh
elif [ $1 = "test" ];
then
    echo -e "==> \033[1;35mrunning 'TEST' build...\033[0m";
    ./build/test.sh
elif [ $1 = "stage" ];
then
    echo -e "==> \033[1;35mrunning 'STAGE' build...\033[0m";
    ./build/stage.sh
elif [ $1 = "prod" ];
then
    echo -e "==> \033[1;35mrunning 'PROD' build...\033[0m";
    ./build/prod.sh
else
    echo -e "==> \033[1;31mEnvironment not registered. Stop.\033[0m"
fi

echo -e "==> \033[1;32mdone\033[0m";