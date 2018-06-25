#!/bin/bash

#
# Common variables
#

# install common scripts if doesn't exist and always pull latest
function check_common_repo() {
  local scriptsDirectory=${1:-$(dirname $0)}

  if [[ -n $GIT_PRIVATE_KEY_PATH ]]
  then
    export GIT_SSH_COMMAND="ssh -i ${GIT_PRIVATE_KEY_PATH}"
  fi
  if [[ ! -d "$scriptsDirectory/common" ]]
  then
    git clone ssh://git@stash.unister.lan:2200/flight/infrastructure.deployment-scripts.git "$scriptsDirectory/common"
  else
    pushd "$scriptsDirectory/common"
    git pull
    popd
  fi
}

# find the base scripts directory and use the common dir of this dir instead of cloning it into each subdir where the base.sh is sourced
function find_base_scripts_directory() {
    local current=$0
    while [ true ]
    do
        current=$(dirname $current)
        if [[ -f "$current/base.sh" ]]
        then
            echo "$current"
            exit 0
        fi
    done
}

scriptsDirectory=$(find_base_scripts_directory)

# Execute when file is sourced
[[ $_ != $0 ]] && check_common_repo "$scriptsDirectory"

source "$scriptsDirectory/common/base.sh"

build_image=amadeus-php-base-build
nginx_image=${REGISTRY}/flight/invia/service/amadeus/nginx
app_image=${REGISTRY}/flight/invia/service/amadeus/app
composer_version=1.5.2