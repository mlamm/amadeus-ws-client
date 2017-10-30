#!/bin/bash
#
# Deploy an application on Minikube with appropriate environment variables
#

set -e

source $(dirname $0)/base.sh

# check requirements
command -v minikube >/dev/null 2>&1 || { echo >&2 "I require kubectl but it's not installed. Aborting... (https://kubernetes.io/docs/tasks/tools/install-kubectl/)"; exit 1; }


#
# Variables
#

export APP_NAME="amadeus"
export ENVIRONMENT="development"
export TEAM_NAME="dev"

export K8S_CONTEXT="minikube"
export K8S_NAMESPACE="default"

export AUTO_ROLLBACK="false"
export DEPLOYMENT_TIMEOUT="2m"


#
# Commands
#

task=$1
case $task in
  up)
    info "Deploying application to \"${K8S_NAMESPACE}\" namespace"
    ./scripts/deploy.sh
    success "Done"
    ;;
  down)
    info "Shutting down application"
    $(dirname $0)/k8s-context.sh
    cat ./kubernetes.yml | envsubst | kubectl delete -f - --ignore-not-found
    success "Done"
    ;;
  *)
  error "Command not found, choose one of the following otions:"
  error "  - deploy ./minikube.sh up"
  error "  - cleanup deployment ./minikube.sh down"
  ;;
esac
