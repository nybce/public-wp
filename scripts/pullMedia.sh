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
echo "Downloading media"
echo "/site/azcopy_linux_amd64_10.13.0/azcopy cp 'https://${MICROSOFT_AZURE_ACCOUNT_NAME}.blob.core.windows.net/${MICROSOFT_AZURE_STORAGES_CONTAINER}/*?sv=${MICROSOFT_AZURE_STORAGES_SAS}' '/site/uploads/' --recursive"
docker exec -i -t wp bash -c "/site/azcopy_linux_amd64_10.13.0/azcopy cp 'https://${MICROSOFT_AZURE_ACCOUNT_NAME}.blob.core.windows.net/${MICROSOFT_AZURE_STORAGES_CONTAINER}/*?sv=${MICROSOFT_AZURE_STORAGES_SAS}' '/site/uploads/' --recursive"
docker exec -i -t wp bash -c "tar -cvf /nybc-wp-${1}-media-${DATE}.tar /site/uploads"
docker cp wp:/nybc-wp-${1}-media-${DATE}.tar ./media
