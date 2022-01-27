#!/bin/bash
set -a
ansible-vault decrypt /envs/dev.env --vault-password-file=/echo_ansible_vault_pass.sh
source /envs/dev.env
cp /envs/dev.env /site/.env
exec "$@"
