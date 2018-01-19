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
: ${KUBETOKEN_HOST?"Variable KUBETOKEN_HOST is mandatory"}
: ${KUBETOKEN_USERNAME?"Variable KUBETOKEN_USERNAME is mandatory"}
: ${KUBETOKEN_PASSWORD?"Variable KUBETOKEN_PASSWORD is mandatory"}
fi

info "Setting Kubernetes cluster context..."

if [ -z "${K8S_CONTEXT}" ]
then
  info "Using kubetoken to authenticate..."
  kubetoken -k -u $KUBETOKEN_USERNAME -P $KUBETOKEN_PASSWORD -h $KUBETOKEN_HOST
  K8S_CONTEXT=$(kubectl config current-context)
fi

info "Setting correct namespace..."
kubectl config set-context ${K8S_CONTEXT} --namespace=${K8S_NAMESPACE}
kubectl config use-context ${K8S_CONTEXT}

success "Successfully changed context to \"${K8S_CONTEXT}\""
