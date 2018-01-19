#!/bin/bash
#
# Test application

set -e

source $(dirname $0)/base.sh

service_endpoint="http://localhost:80/health"

info "Bringing services up with docker compose"
docker-compose up -d

# Graceful shutdown of the tests, it stops docker-compose
# in case of error or at the end of the execution
function gracefull_shutdown() {
  exit_code=$?

  trap '' EXIT

  info "Stopping and removing containers"
  docker-compose down

  if [ "$exit_code" -ne "0" ]
  then
    error "Tests failed"
    exit 1
  fi

  success "Done"
}

# On error or when finished, shutdown containers
trap 'gracefull_shutdown' ERR EXIT

# check readiness before running tests
timeout="300"
status_code="500"
while [ "$status_code" -lt 200 -o "$status_code" -ge 400 ]
do
    info "Waiting for the application to be ready"
    status_code=$(curl -s -o /dev/null -w "%{http_code}" $service_endpoint)

    timeout=$[$timeout-1]
    if [ "$timeout" = 0 ]
    then
      echo "App seems to be broken as it don't come up. Aborting..."
      exit 1
    fi

    sleep 1
done

info "Running tests"
docker-compose exec -T \
  amadeus-php \
  /var/www/vendor/bin/codecept run -v --steps --no-interaction
