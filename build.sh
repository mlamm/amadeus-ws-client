#!/bin/bash
echo -e "\n";

if [ $1 = "unidev" ];
then
    echo -e "==> \032[1;35mRunning 'UNIDEV' build...\032[0m";
    ./build/unidev.sh
elif [ $1 = "dev" ];
then
    echo -e "==> \033[1;35mRunning 'DEV' build...\033[0m";
    ./build/dev.sh
elif [ $1 = "test" ];
then
    echo -e "==> \033[1;35mRunning 'TEST' build...\033[0m";
    ./build/test.sh
elif [ $1 = "stage" ];
then
    echo -e "==> \033[1;35mRunning 'STAGE' build...\033[0m";
    ./build/stage.sh
elif [ $1 = "prod" ];
then
    echo -e "==> \033[1;35mRunning 'PROD' build...\033[0m";
    ./build/prod.sh
elif [ $1 = "unidev" ];
then
    echo -e "==> \033[1;35mRunning 'UNIDEV' build...\033[0m";
    build/unidev.sh
else
    echo -e "==> \033[1;31mEnvironment not registered. Stop.\033[0m"
fi

echo -e "==> \033[1;32mdone\033[0m";
