#!/bin/bash

#
# Common variables
#

GREEN="\033[2;32m"
RED="\033[0;31m"
RESET="\033[0;0m"
YELLOW="\033[2;33m"

tag=$(git describe --tag --abbrev=0 | tr + _)
export TAG=${TAG:-$tag}
export REGISTRY=${REGISTRY:-"630542070554.dkr.ecr.eu-central-1.amazonaws.com"}
buildImage=amadeus-php-base-build

#
# Functions
#

function error() {
  echo -e "[ERROR] ${RED}${1}${RESET}"
}

function info() {
  echo -e "[INFO] ${YELLOW}${1}${RESET}"
}

function success() {
  echo -e "[SUCCESS] ${GREEN}${1}${RESET}"
}

# Exponentially retry 5 times a given command
function retry() {
  NEXT_WAIT_TIME=0
  set +e
  until $1 || [ $NEXT_WAIT_TIME -eq 4 ]; do
    ((NEXT_WAIT_TIME++))
    sleep_time=$((NEXT_WAIT_TIME**NEXT_WAIT_TIME))

    error "Failed executing command, ${NEXT_WAIT_TIME} attempt(s):"
    error "  > ${1}"
    error "Retrying in ${sleep_time} seconds"
    sleep $sleep_time
  done
  set -e
}

function getMountPath(){
  # If build docker imags using Docker from Minikube, we mount files from the
  # /hosthome directory instead of /home directory.
  # See: https://kubernetes.io/docs/getting-started-guides/minikube/#mounted-host-folders
  mount_path=${1:-$PWD}
  if [[ "${DOCKER_CERT_PATH}" =~ ^.*minikube.*$ ]] && [[ `uname -s` == "Linux" ]]
  then
    info "Building from Linux using Docker Minikube"
    info "Setting mounting path to /hosthome/ directory instead of /home/"
    mount_path="${mount_path/\/home\///hosthome/}"
    info "new current working dir: $mount_path"
  fi
}
