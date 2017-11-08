#!/bin/bash
# TODO: Investigate the following error message:
#       oci runtime error: exec failed: container_linux.go:265: starting container process caused "exec: \"vendor/bin/codecept\": stat vendor/bin/codecept: no such file or directory"
#       Got it when running ./scripts/codecept right after bringing docker-compose up.
#       Maybe something related to the container/service not yet being ready for consumption?

source $(dirname $0)/base.sh

function prepare {
  info "Bringing services up with docker compose"
  docker-compose up -d
  # Docker Compose comes up rather too fast. When we run the tests, the services are not
  # completely up just yet, so we are getting false positives.

  # check readiness before running tests
  timeout="30"
  exitCode="1"
  while [ "$exitCode" != 0 ]
  do
      docker-compose exec -T amadeus-php ash /var/www/scripts/health/readiness.sh
      exitCode=$?
      timeout=$[$timeout-1]
      if [ "$timeout" = 0 ]; then
        echo "App seems to be broken as it don't come up. Aborting..."
        exit 1;
      fi

      sleep 1
  done
}

function run_tests {
  info "Running tests"
  ./scripts/codecept.sh run
}

function cleanup {
  info "Stopping and removing containers"
  docker-compose down
}

prepare

if [ "$?" -ne "0" ]
then
  error "Failed bringing docker compose up"
  exit $?
fi

run_tests

testResult=$?
if [ "$testResult" -ne "0" ]
then
  error "Failed while trying to run tests"
else
  success "Done"
fi

cleanup
exit "$testResult"
