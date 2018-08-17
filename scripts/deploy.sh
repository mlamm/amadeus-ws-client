#!/bin/bash
#
# Deploy Kubernetes resources using Helm
#

set -e

source $(dirname $0)/base.sh

config_file="./config/${ENVIRONMENT}.dist.yml"

if [ ! -f $config_file ]
then
  error "Application configuration file ${config_file} does not exist"
  exit 1
fi

info "Initialising Kubernets context"
switch_k8s_context

info "Initialising Helm"
helm init --service-account=tiller \
  --client-only \
  --tiller-namespace=${K8S_NAMESPACE}

info "Using config file ${config_file}"
echo $APP_CONFIG

helm upgrade -i amadeus-v${major_version} \
  --set=php.app.image.tag=${TAG} \
  --set=php.app.config.'app\.yml'=$(cat $config_file | openssl base64 -A) \
  --set=php.nginx.image.tag=${TAG} \
  --set=php.ingress.routes[0].host=amadeus-v${major_version}.search.dev.invia.io \
  --set=php.ingress.routes[1].host=amadeus-v${major_version}.search.prod.invia.io \
  --set=php.ingress.trafficType=${TRAFFIC_TYPE} \
  --namespace=${K8S_NAMESPACE} \
  --tiller-namespace=${K8S_NAMESPACE} \
  --force \
  --debug \
  --wait \
  ./charts

success "Application successfully deployed"
