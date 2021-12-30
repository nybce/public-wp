#!/bin/bash
DATE=$(date +"%Y-%m-%d")
set -e
echo "Decrypting environment variables"
docker exec -i -t wp bash -c "ansible-vault decrypt /envs/${1}.env --vault-password-file=.vaultpass"
docker cp wp:/envs/${1}.env .env/${1}.env
echo "Sourcing environment variables"
source .env/${1}.env
echo "Reencrypting environment variables"
docker exec -i -t wp bash -c "ansible-vault encrypt /envs/${1}.env --vault-password-file=.vaultpass"
echo "Uploading media"
docker exec -i -t wp bash -c "/site/azcopy_linux_amd64_10.13.0/azcopy sync '/site/web/app/uploads/' 'https://${MICROSOFT_AZURE_ACCOUNT_NAME}.blob.core.windows.net/${MICROSOFT_AZURE_STORAGES_CONTAINER}/?sv=${MICROSOFT_AZURE_STORAGES_SAS}' --recursive"
