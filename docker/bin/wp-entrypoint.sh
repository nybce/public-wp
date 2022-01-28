#!/bin/bash
set -a
echo "upgrade composer dependencies"
composer install -n
composer update -vvv -n
# compose update
exec "$@"
