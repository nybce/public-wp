#!/usr/bin/env bash
set -e
source /envs/${1}.env
DATE=$(date +"%Y-%m-%d")
mysqldump -h $DB_HOST -u $DB_USER -p$DB_PASSWORD $DB_NAME > /db_dumps/nybc-wordpress-${1}-dump-$DATE.sql
sed 's#.local.org#.dev.nybc-wordpress.bbox.ly#g' /db_dumps/nybc-wordpress-${1}-dump-$DATE.sql > /db_dumps/nybc-wordpress-${1}-to-${2}-$DATE.sql
scp -i '/envs/key' /db_dumps/nybc-wordpress-${1}-to-${2}-$DATE.sql blenderorders@52.87.95.160:~/
source /envs/${2}.env
ssh -i '/envs/key'  -o "StrictHostKeyChecking no" "blenderorders@52.87.95.160" "mysqladmin -h $DB_HOST -u $DB_USER -p$DB_PASSWORD  --force drop db"
ssh -i '/envs/key'  -o "StrictHostKeyChecking no" "blenderorders@52.87.95.160" "mysqladmin -h $DB_HOST -u $DB_USER -p$DB_PASSWORD  --force create db"
ssh -i '/envs/key'  -o "StrictHostKeyChecking no" "blenderorders@52.87.95.160" "mysql -h $DB_HOST -u $DB_USER -p$DB_PASSWORD $DB_NAME < nybc-wordpress-${1}-to-${2}-$DATE.sql"
ssh -i '/envs/key'  -o "StrictHostKeyChecking no" "blenderorders@52.87.95.160 rm  nybc-wordpress-${1}-to-${2}-$DATE.sql"
