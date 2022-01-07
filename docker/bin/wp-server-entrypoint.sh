#!/bin/bash
set -a
echo "upgrade composer dependencies"
composer update -vvv -n
chmod 777 /envs/dev.env
chmod 777 /echo_ansible_vault_pass.sh
bash /echo_ansible_vault_pass.sh > /envs/.vaultpass
ansible-vault decrypt /envs/dev.env --vault-password-file=/echo_ansible_vault_pass.sh
source /envs/dev.env
cp /envs/dev.env /site/.env
exec "$@"
