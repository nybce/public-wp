#!/bin/bash
set -a
echo "upgrade composer dependencies"
if ${UPDATE_COMPOSER:=false}; then
    composer install -n
    composer update -vvv -n
fi
# compose update
exec "$@"
