#!/bin/bash
if [ $1 = "dev" ];
then
    ./build/dev.sh
elif [ $1 = "test" ];
then
    ./build/test.sh
elif [ $1 = "stage" ];
then
    ./build/stage.sh
elif [ $1 = "prod" ];
then
    ./build/prod.sh
else
    echo "environment not registered. stop."
fi