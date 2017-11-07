#!/bin/bash

echo "clean up nginx logs..."
docker run -it --rm -v $(pwd):/app -w /app busybox rm var/logs/nginx/*.log -rf
echo "clean up other logs..."
docker run -it --rm -v $(pwd):/app -w /app busybox rm var/logs/*.log -rf
