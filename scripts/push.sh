#!/bin/bash
#
# Push Docker container to given registry using AWS credentials
#

set -e

source $(dirname $0)/base.sh

# Log into Docker private registry
docker_login

info "Pushing images"
docker push ${REGISTRY}/flight/invia/service/amadeus/app
docker push ${REGISTRY}/flight/invia/service/amadeus/nginx

info "Logout from Docker registry"
docker logout

