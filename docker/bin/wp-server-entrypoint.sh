#!/bin/bash
set -a
bash /echo_ansible_vault_pass.sh > /envs/.vaultpass
ansible-vault decrypt /site/.env --vault-password-file=/envs/.vaultpass
source /site/.env
exec "$@"
