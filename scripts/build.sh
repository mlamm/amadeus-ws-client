#!/bin/bash
#
# Build
#

set -e

source $(dirname $0)/base.sh

info "Downloading composer if not present..."
download_composer $composer_version

info "Cleaning old logs..."
./scripts/local/clean-logs.sh

info "Cleaning old caches..."
./scripts/local/clean-cache.sh

info "Preparing directories..."
chmod -R 777 var/

info "Generating documentation..."
generate_docs_aglio "-i docs/api.apib -o var/docs/index.html --theme-variables flatly --theme-full-width"

info "Setting default config..."
cp config/development.dist.yml config/app.yml

info "Building Nginx image..."
docker build -t $nginx_image:$TAG -t $nginx_image:latest -f scripts/docker/nginx/Dockerfile .

info "Building PHP base image..."
docker build -t $build_image -f scripts/docker/php/Dockerfile .

info "Installing PHP dependencies..."
composer_install $build_image

info "Building app image..."
docker build -t $app_image:$TAG -t $app_image:latest -f scripts/docker/php/Dockerfile .

success "done!"
