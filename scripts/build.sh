#!/bin/bash
#
# Build
#

set -e

source $(dirname $0)/base.sh

docker_image=$(awk '/FROM/{print $2}' scripts/docker/php/Dockerfile)
buildImage=php-base-build
COMPOSER_VERSION=1.5.2

function build() {
  info "building app..."
  configure
  createBinaries
  #createDocs
  prepareDirectories
  buildImages

  success "done!"
}

function buildImages(){
  info "Creating docker images..."

  # Build Nginx image with tag from git commit hash
  nginx_image="$REGISTRY/flight/invia/service/amadeus/nginx"
  docker build -t $nginx_image:$TAG -f scripts/docker/nginx/Dockerfile .

  # Tag Nginx image with "latest"
  docker tag $nginx_image:$TAG $nginx_image:latest

  # Build app base image used for composer
  docker build -t $buildImage -f scripts/docker/php/Dockerfile .

  # Build PHP image
  # If AWS credentials are provided we check if there is an archived composer
  # file in S3 bucket, if there is no archive, use the same php image to
  # download all the composer dependencies, then create the archive and upload
  # it to S3. Default, install all dependencies on build time.
  app_image="$REGISTRY/flight/invia/service/amadeus/app"

  # Make sure we have the Git private setup for composer to install
  # dependencies from the stash.unister.lan git private repo
  if [[ -n $GIT_PRIVATE_KEY ]]
  then
    mkdir -p ~/.ssh
    echo "${GIT_PRIVATE_KEY}" > ~/.ssh/id_stash_unister_lan
    export GIT_SSH_COMMAND="ssh -i ~/.ssh/id_stash_unister_lan"
  fi

  if [[ -n $AWS_ACCESS_KEY_ID && -n $AWS_SECRET_ACCESS_KEY && -n $AWS_COMPOSER_CACHE_S3_BUCKET && -e "composer.json" ]]
  then
    archive_name=`md5sum composer.json | awk '{print $1}'`.tar.gz

    info "File archive hash is ${archive_name}"

    set +e
    aws s3 ls ${AWS_COMPOSER_CACHE_S3_BUCKET}/${archive_name} > /dev/null
    archive_exist=$?
    set -e

    if [ "$archive_exist" = "0" ]
    then
      info "Archive found, downloading it..."
      aws s3 cp ${AWS_COMPOSER_CACHE_S3_BUCKET}/${archive_name} .
      tar -xf $archive_name
    else
      info "No archive found, installing dependencies..."
      bash ./scripts/composer install

      info "Archiving vendor directory to ${AWS_COMPOSER_CACHE_S3_BUCKET}/${archive_name}"
      tar -czf $archive_name vendor
      aws s3 cp $archive_name ${AWS_COMPOSER_CACHE_S3_BUCKET}/${archive_name}
    fi

    # Remove archive
    rm $archive_name

    # Remove composer install command
    sed -i '/RUN composer install/d' scripts/docker/php/Dockerfile
  else
    info "Not using cached dependencies. Can't find \$AWS_ACCESS_KEY_ID, \$AWS_SECRET_ACCESS_KEY, \$AWS_COMPOSER_CACHE_S3_BUCKET or composer.json file."
    bash ./scripts/composer install
  fi

  # Build app image with tag from git commit hash
  docker build -t $app_image:$TAG -f scripts/docker/php/Dockerfile .

  # Tag app image with "latest"
  docker tag $app_image:$TAG $app_image:latest
}

function createBinaries(){
  info "Creating binaries..."

  # Build helper scripts
  echo -e '#!/bin/sh\n\ndocker run --rm -v $(pwd):/var/www -w /var/www christianbladescb/aglio -i var/docs/api/api.apib -o web/docs/index.html --theme-variables flatly --theme-full-width' > scripts/create-docs
  echo -e '#!/bin/sh\n\ndocker run --rm -e GIT_SSH_COMMAND="ssh -i ~/.ssh/id_stash_unister_lan" -v ~/.ssh:/root/.ssh -v ~/.composer:/root/.composer -v $(pwd):/var/www -w /var/www '$buildImage' php composer.phar "$@"' > scripts/composer

    if [ ! -f composer.phar ]; then
        wget -O composer.phar https://getcomposer.org/download/$COMPOSER_VERSION/composer.phar
    fi

  chmod +x ./scripts/*
}

function createDocs(){
  info "Creating docs..."
  scripts/create-docs
}

function configure(){
  info "Setting config..."
  cp config/development.dist.yml config/app.yml
}
function prepareDirectories(){
    chmod -R 777 var/
}


build
