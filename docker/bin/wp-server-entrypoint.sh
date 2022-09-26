#!/bin/bash
set -a
echo "upgrade composer dependencies"
ansible-vault decrypt /site/.env --vault-password-file=/.vaultpass
chmod 777 /site/.env
source /site/.env
/usr/sbin/sshd &
exec "$@"
