#!/bin/bash
set -a
echo "upgrade composer dependencies"
composer update -vvv -n
chmod 777 /envs/staging.env
ansible-vault decrypt /envs/staging.env --vault-password-file=/.vaultpass-test
source /envs/staging.env
cp /envs/staging.env /site/.env
composer install -n
exec "$@"
