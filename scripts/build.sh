#!/bin/bash
#
# Build
#

set -e

source $(dirname $0)/base.sh

info "Downloading composer if not present..."
download_composer $composer_version

info "Cleaning old directories..."
./scripts/local/clean-directories.sh

info "Generating documentation..."
generate_docs_aglio "-i docs/api.apib -o var/docs/index.html --theme-variables flatly --theme-full-width"

info "Setting default config..."
cp config/development.dist.yml config/app.yml

info "Building Nginx image..."
build_or_pull_base_nginx
docker build -t $nginx_image:$TAG -t $nginx_image:latest -f scripts/docker/nginx/Dockerfile .

info "Building PHP base image..."
build_or_pull_base_app_image
docker build -t $app_image:build -f scripts/docker/php/Dockerfile .

info "Installing PHP dependencies..."
composer_install $app_image:build

info "Building app image..."
target_environment=production
if [ "$1" == "dev" ]; then
    info "Building app image with dev dependencies..."
    target_environment=development
fi
docker build --target "$target_environment" -t $app_image:$TAG -t $app_image:latest -f scripts/docker/php/Dockerfile .

success "done!"
