#!/bin/bash
#
# Push Docker container to given registry using AWS credentials
#

set -e

source $(dirname $0)/base.sh

: ${AWS_ACCESS_KEY_ID?"Variable AWS_ACCESS_KEY_ID is mandatory"}
: ${AWS_SECRET_ACCESS_KEY?"Variable AWS_SECRET_ACCESS_KEY is mandatory"}
: ${AWS_REGION?"Variable AWS_REGION is mandatory"}
: ${REGISTRY?"Variable REGISTRY is mandatory"}

info "Getting Docker registry credentials"
docker_login=$(aws ecr get-login --no-include-email --region=${AWS_REGION})

info "Login Docker to registry"
eval "${docker_login}"

info "Pushing images"
docker push ${REGISTRY}/flight/invia/service/amadeus/app
docker push ${REGISTRY}/flight/invia/service/amadeus/nginx

info "Logout from Docker registry"
docker logout
