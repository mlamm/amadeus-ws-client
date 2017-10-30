#!/bin/bash
#
# Switch Kubernetes context

set -e

# check requirements
command -v kubectl >/dev/null 2>&1 || { echo >&2 "I require kubectl but it's not installed. Aborting... (https://kubernetes.io/docs/tasks/tools/install-kubectl/)"; exit 1; }

source $(dirname $0)/base.sh

# Mandatory and optional variables
if [ -z "$K8S_CONTEXT" ]
then
: ${K8S_CA_PATH:="Variable K8S_CA_PATH is mandatory"}
: ${K8S_HOST:="Variable K8S_HOST is mandatory"}
: ${K8S_PASSWORD:="Variable K8S_PASSWORD is mandatory"}
: ${K8S_USERNAME:="Variable K8S_USERNAME is mandatory"}
fi

info "Setting Kubernetes cluster context..."

if [ -z "${K8S_CONTEXT}" ]
then
  K8S_CONTEXT="context"
  kubectl config set-credentials jenkins \
    --username=${K8S_USERNAME} \
    --password=${K8S_PASSWORD}
  kubectl config set-cluster cluster \
    --server=${K8S_HOST} \
    --certificate-authority=${K8S_CA_PATH}
  kubectl config set-context context \
    --cluster=cluster \
    --user=jenkins
fi

# switch to correct namespace
kubectl config set-context ${K8S_CONTEXT} --namespace=${K8S_NAMESPACE}
kubectl config use-context ${K8S_CONTEXT}

success "Successfully changed context to \"${K8S_CONTEXT}\""
