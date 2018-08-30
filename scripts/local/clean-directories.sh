#!/bin/bash

set -e

source $(dirname $0)/../base.sh

info "Clean up logs directory..."
docker run --rm -v $(pwd):/app -w /app busybox rm var/logs -rf

info "Clean up cache directory..."
docker run --rm -v $(pwd):/app -w /app busybox rm var/cache -rf

info "Clean up cache directory..."
docker run --rm -v $(pwd):/app -w /app busybox rm var/docs -rf

info "Preparing directories..."
mkdir -p var/logs
mkdir -p var/cache
mkdir -p var/docs