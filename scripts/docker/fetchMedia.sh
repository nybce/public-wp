#!/bin/bash
ansible-vault decrypt /envs/${1}.env --vault-password-file=/envs/.vaultpass
ansible-vault decrypt /envs/key --vault-password-file=/envs/.vaultpass
source /envs/${1}.env
DATE=$(date +"%Y-%m-%d")
ssh -i '/envs/key'  -o "StrictHostKeyChecking no" $SITE_SSH "tar -C /home/wpe-user/sites/$SITE_ROOT/wp-content/uploads -cf /home/wpe-user/sites/$SITE_ROOT/_wpeprivate/$SITE_NAME-media-$DATE.tar"
scp -i '/envs/key' $SITE_SSH:/home/wpe-user/sites/$SITE_ROOT/_wpeprivate/$SITE_NAME-media-$DATE.tar /site/web/app/uploads
ssh -i '/envs/key' $SITE_SSH "rm /home/wpe-user/sites/$SITE_ROOT/_wpeprivate/$SITE_NAME-media-$DATE.tar"
tar -xf /site/web/app/uploads/$SITE_NAME-media-$DATE.tar
ansible-vault encrypt /envs/${1}.env --vault-password-file=/envs/.vaultpass
ansible-vault encrypt /envs/key --vault-password-file=/envs/.vaultpass
