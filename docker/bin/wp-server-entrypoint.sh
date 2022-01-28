#!/bin/bash
set -a
echo "upgrade composer dependencies"
ansible-vault decrypt /envs/${1}.env --vault-password-file=/.vaultpass
chmod 777 /envs/${1}.env
source /envs/${1}.env
cp /envs/${1}.env /site/.env
exec "$@"
