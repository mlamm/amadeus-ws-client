#!/bin/bash
#
# Test application

set -e

source $(dirname $0)/base.sh

service_endpoint="http://localhost:80/health"

info "Setting configuration"
cp config/{development.dist,app}.yml

info "Bringing services up with docker compose"
docker-compose -f ./docker-compose.yml up -d

# On error or when finished, shutdown containers
trap 'gracefull_shutdown_docker_compose "-f ./docker-compose.yml"' ERR EXIT

# check readiness before running tests
wait_for_endpoint $service_endpoint

info "Running tests"
./scripts/local/codecept.sh run -v --steps --no-interaction
