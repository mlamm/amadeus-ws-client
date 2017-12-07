#!/bin/bash
source $(dirname $0)/base.sh
info "clean up jms serializer cache..."
docker run --rm -v $(pwd):/app -w /app busybox rm var/cache/serializer/* -rf
info "clean up profiler cache..."
docker run --rm -v $(pwd):/app -w /app busybox rm var/cache/profiler/* -rf
info "clean up twig cache..."
docker run --rm -v $(pwd):/app -w /app busybox rm var/cache/twig/* -rf
info "clean up config cache..."
docker run --rm -v $(pwd):/app -w /app busybox rm var/cache/config -rf
