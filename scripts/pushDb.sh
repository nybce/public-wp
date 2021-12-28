#!/bin/bash
DATE=$(date +"%Y-%m-%d")
scp ./db_dumps/${1} blenderbox-bastion:~/nybc-db/${2}-to-dev-DATE=$(date +"%Y-%m-%d").sql
set -e
source .env/local.env
ssh blenderbox-bastion "sed 's#.local.org#.dev.nybc-wordpress.bbox.ly#g' ~/nybc-db/${2}-to-dev-DATE=$(date +"%Y-%m-%d").sql > ~/nybc-db/${2}-to-dev-DATE=$(date +"%Y-%m-%d").sql"
ssh blenderbox-bastion "sed 's#stage.nybc-wordpress.bbox.ly#.dev.nybc-wordpress.bbox.ly#g' ~/nybc-db/${2}-to-dev-DATE=$(date +"%Y-%m-%d").sql > ~/nybc-db/${2}-to-dev-DATE=$(date +"%Y-%m-%d").sql"
ansible-vault decrypt /envs/dev.env --vault-password-file=.vaultpass
source .env/dev.env
ansible-vault encrypt /envs/dev.env --vault-password-file=/envs/.vaultpass
ssh blenderbox-bastion "mysqladmin -h $DB_HOST -u $DB_USER -p$DB_PASSWORD  --force drop db"
ssh blenderbox-bastion "mysqladmin -h $DB_HOST -u $DB_USER -p$DB_PASSWORD  --force create db"
ssh blenderbox-bastion "mysql -h $DB_HOST -u $DB_USER -p$DB_PASSWORD $DB_NAME < ~/nybc-db/${2}-to-dev-DATE=$(date +"%Y-%m-%d").sql"
