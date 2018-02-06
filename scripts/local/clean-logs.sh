#!/bin/bash

set -e

source $(dirname $0)/../base.sh

info "clean up nginx logs..."
docker run --rm -v $(pwd):/app -w /app busybox rm var/logs/nginx/*.log -rf

info "clean up other logs..."
docker run --rm -v $(pwd):/app -w /app busybox rm var/logs/*.log -rf
