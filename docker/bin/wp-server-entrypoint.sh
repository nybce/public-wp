#!/bin/bash
set -a
echo "upgrade composer dependencies"
ansible-vault decrypt /envs/staging.env --vault-password-file=/.vaultpass
chmod 777 /envs/staging.env
source /envs/staging.env
cp /envs/staging.env /site/.env
composer update -vvv -n
composer install -n
exec "$@"
