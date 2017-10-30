#!/bin/bash

echo "clean up nginx logs..."
docker exec -it service-amadeus-php rm var/logs/nginx/*.log -rf
echo "clean up other logs..."
docker exec -it service-amadeus-php rm var/logs/*.log -rf
