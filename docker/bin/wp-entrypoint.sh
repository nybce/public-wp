#!/bin/bash
set -a
echo "upgrade composer dependencies"
composer update -vvv
# compose update
exec "$@"
