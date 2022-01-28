#!/bin/bash
set -a
echo "upgrade composer dependencies"
ansible-vault decrypt /envs/dev.env --vault-password-file=/.vaultpass
chmod 777 /envs/dev.env
source /envs/dev.env
cp /envs/dev.env /site/.env
exec "$@"
