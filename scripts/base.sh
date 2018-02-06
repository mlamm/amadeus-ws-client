#!/bin/bash

#
# Common variables
#

# install common scripts if doesn't exist and always pull latest
function check_common_repo() {
  if [[ -n $GIT_PRIVATE_KEY_PATH ]]
  then
    export GIT_SSH_COMMAND="ssh -i ${GIT_PRIVATE_KEY_PATH}"
  fi
  if [[ ! -d $(dirname $0)/common ]]
  then
    git clone ssh://git@stash.unister.lan:2200/flight/infrastructure.deployment-scripts.git $(dirname $0)/common
  else
    pushd $(dirname $0)/common
    git pull
    popd
  fi
}

# Execute when file is sourced
[[ $_ != $0 ]] && check_common_repo

source $(dirname $0)/common/base.sh

build_image=amadeus-php-base-build
nginx_image=${REGISTRY}/flight/invia/service/amadeus/nginx
app_image=${REGISTRY}/flight/invia/service/amadeus/app
composer_version=1.5.2
