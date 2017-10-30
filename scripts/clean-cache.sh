#!/bin/bash

echo "clean up profiler cache..."
docker exec -it service-amadeus-php rm var/cache/profiler/* -rf
echo "clean up twig cache..."
docker exec -it service-amadeus-php rm var/cache/twig/* -rf
