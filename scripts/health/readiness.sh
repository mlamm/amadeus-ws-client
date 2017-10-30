#!/bin/sh
# Script to check the readiness state of the php application via php-fpm

# Make sure you installed 'libfcgi0ldbl' on the system where you run this script.
command -v cgi-fcgi >/dev/null 2>&1 || { echo >&2 "I require cgi-fcgi (from package libfcgi0ldbl) but it's not installed. Aborting..."; exit 1; }

# make a GET to /
expected="I am alive."
output=$(SCRIPT_NAME=/index.php \
SCRIPT_FILENAME=/var/www/web/index.php \
DOCUMENT_ROOT=/var/www/web/ \
REQUEST_METHOD=GET \
REQUEST_URI=/health \
cgi-fcgi -bind -connect 127.0.0.1:9000 | grep "$expected")

if [ "$expected" != "$output" ]; then
    exit 1;
else
    exit 0;
fi
