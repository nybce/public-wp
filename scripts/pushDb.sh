#!/bin/bash
DATE=$(date +"%Y-%m-%d")
scp ./db_dumps/${1} blenderbox-bastion:~/${2}-to-${3}-${DATE}.sql
set -e
source .env/local.env
ssh blenderbox-bastion "sed 's#.local.org#.${3}.nybc-wordpress.bbox.ly#g' ~/${2}-to-${3}-${DATE}.sql > ~/${2}-to-${3}-${DATE}-replaced.sql"
ssh blenderbox-bastion "sed 's#http://nybc-enterprise.${3}.nybc-wordpress.bbox.ly/app/uploads/#https://7rcmher38fkopqq0ks.blob.core.windows.net/storage-wordpress-web-${3}/#g' ~/${2}-to-${3}-${DATE}-replaced.sql > ~/${2}-to-${3}-${DATE}.sql"
echo "Decrypting environment variables"
docker exec -i -t wp bash -c "ansible-vault decrypt /envs/${3}.env --vault-password-file=.vaultpass"
docker cp wp:/envs/${3}.env .env/${3}.env
echo "Sourcing environment variables"
source .env/${3}.env
echo "Reencrypting environment variables"
docker exec -i -t wp bash -c "ansible-vault encrypt /envs/${3}.env --vault-password-file=.vaultpass"
docker cp wp:/envs/${3}.env ./.env/${3}.env
ssh blenderbox-bastion "mysqladmin -h $DB_HOST -u $DB_USER -p$DB_PASSWORD  --force drop db"
ssh blenderbox-bastion "mysqladmin -h $DB_HOST -u $DB_USER -p$DB_PASSWORD  --force create db"
ssh blenderbox-bastion "mysql -h $DB_HOST -u $DB_USER -p$DB_PASSWORD $DB_NAME < ~/${2}-to-${3}-${DATE}.sql"
