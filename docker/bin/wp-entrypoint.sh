#!/bin/bash
set -a
source /.env
echo "upgrade composer dependencies"
composer update -vvv
# compose update
exec "$@"
