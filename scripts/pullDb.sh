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
docker cp wp:/envs/${1}.env ./.env/${1}.env
echo "Dumping DB"
ssh blenderbox-bastion "mysqldump -h $DB_HOST -u $DB_USER -p$DB_PASSWORD $DB_NAME  > nybc-wp-${1}-to-local-${DATE}.sql"
echo "Find/Replace DB"
ssh blenderbox-bastion "sed 's#.${1}.nybc-wordpress.bbox.ly#.local.org#g' ~/nybc-wp-${1}-to-local-${DATE}.sql > ~/nybc-wp-${1}-to-local-${DATE}-replaced.sql"
ssh blenderbox-bastion "sed 's#https://7rcmher38fkopqq0ks.blob.core.windows.net#http://nybc-enterprise.${1}.nybc-wordpress.bbox.ly#g' ~/nybc-wp-${1}-to-local-${DATE}-replaced.sql > ~/nybc-wp-${1}-to-local-${DATE}.sql"
ssh blenderbox-bastion "sed 's#storage-wordpress-web-${1}#app/uploads#g' ~/nybc-wp-${1}-to-local-${DATE}.sql > ~/nybc-wp-${1}-to-local-${DATE}-replaced.sql"
echo "Downloading DB"
scp blenderbox-bastion:~/nybc-wp-${1}-to-local-${DATE}-replaced.sql ./db_dumps
