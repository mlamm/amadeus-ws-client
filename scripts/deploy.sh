#!/bin/bash
#
# Deploy Kubernetes resources
# Apply change to Kubernetes, rollback if the deployment didn't succeed
# after DEPLOYMENT_TIMEOUT (default 10minutes)

set -e

# check requirements
command -v kubectl >/dev/null 2>&1 || { echo >&2 "I require kubectl but it's not installed. Aborting... (https://kubernetes.io/docs/tasks/tools/install-kubectl/)"; exit 1; }

source $(dirname $0)/base.sh

#
# Variables
#

# Mandatory and optional variables
: ${APP_NAME?"Variable APP_NAME is mandatory"}
: ${K8S_NAMESPACE?"Variable K8S_NAMESPACE is mandatory"}

# Deployment timeout, if the deployment is not successful after
# this time, a rolling back action is triggered
DEPLOYMENT_TIMEOUT=${DEPLOYMENT_TIMEOUT:-"10m"}

# Automatically rollback deployments in case of failure
AUTO_ROLLBACK=${AUTO_ROLLBACK:-"true"}

# Export variables that are used in Kubernetes
export TAG
export REGISTRY

# Convert all secrets that are used as environment variable to base64
config_file="./config/${ENVIRONMENT}.dist.yml"

if [ ! -f $config_file ]
then
  error "Application configuration file ${config_file} does not exist"
  exit 1
fi

export APP_CONFIG=$(base64 < "${config_file}" | tr -d '\r\n')

info "Using config file ${config_file}"
echo $APP_CONFIG

# Switch context and deploy
#

# Switch context
$(dirname $0)/k8s-context.sh

info "Deploying..."
manifest=$(cat ./kubernetes.yml | envsubst)

info "Manifest to deploy:"
echo -e "\n${manifest}\n"
apply_state=$(echo "${manifest}" | kubectl apply -f -)

info "Applied changes:"
echo -e "\n${apply_state}\n"

# Get all the deployment names from the Kubernetes manifest
deployment_resource_name=$(echo "${apply_state}" | awk '
  /deployment/{
    s=$2
    gsub("\"", "", s)
    print "deploy/" s
}')
deployment_resource_count=$(echo "${deployment_resource_name}" | wc -w)

info "Found ${deployment_resource_count} deployment(s)"


#
# Monitor deployment
#

# undo_deployments rollback all deployments to the latest known revision
function undo_deployments() {
  set +e

  local deployments=$1

  error "Rolling back to previous version"

  for deployment in $deployments
  do
    info "Logs from ${deployment}:"
    kubectl logs $deployment
    info "Rolling back ${deployment}"
    kubectl rollout undo $deployment
  done

  error "Something went wrong with the deployment"
  exit 1
}

# In case we receive SIGINT/SIGTERM, we rollback all deployments
trap 'undo_deployments $deployment_resource_name' SIGINT SIGTERM

# Loop through all the deployments
for deployment in $deployment_resource_name
do
  info "Monitoring rollout of ${deployment}"
  set +e
  kubectl rollout status $deployment --request-timeout=${DEPLOYMENT_TIMEOUT}
  rollout_fail=$?
  set -e

  # Rollback in case of error
  if [ "$rollout_fail" == 1 ] && [ "$AUTO_ROLLBACK" == "true" ]
  then
    error "Deployment reached timeout of ${DEPLOYMENT_TIMEOUT}"
    undo_deployments $deployment_resource_name
    break
  fi

  success "Successfully rolled out ${deployment}"
done

success "Application successfully deployed"
