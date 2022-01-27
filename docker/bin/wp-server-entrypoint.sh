#!/bin/bash
set -a
echo  > /envs/.vaultpass
ansible-vault decrypt /site/.env --vault-password-file=/echo_ansible_vault_pass.sh
source /site/.env
exec "$@"
