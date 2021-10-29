#!/bin/bash
ansible-vault decrypt /envs/${1}.env --vault-password-file=/envs/.vaultpass
ansible-vault decrypt /envs/key --vault-password-file=/envs/.vaultpass
source /envs/${1}.env
DATE=$(date +"%Y-%m-%d")
ssh -i '/envs/key'  -o "StrictHostKeyChecking no" $SITE_SSH "cd /home/wpe-user/sites/$SITE_ROOT;wp db export ./_wpeprivate/$SITE_NAME-$ENVIRONMENT-$DATE.sql"
scp -i '/envs/key' -o "StrictHostKeyChecking no" $SITE_SSH:/home/wpe-user/sites/$SITE_ROOT/_wpeprivate/$SITE_NAME-$ENVIRONMENT-$DATE.sql /db_dumps
ansible-vault encrypt /envs/${1}.env --vault-password-file=/envs/.vaultpass
ansible-vault encrypt /envs/key --vault-password-file=/envs/.vaultpass
