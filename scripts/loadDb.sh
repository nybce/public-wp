#!/bin/bash
set -ex
source .env/local.env
docker exec -it nybc-wordpress_db_1 mysqladmin -u$DB_USER -p$DB_PASSWORD --force drop $DB_NAME
docker exec -it nybc-wordpress_db_1 mysqladmin -u$DB_USER -p$DB_PASSWORD --force create $DB_NAME
docker cp ./db_dumps/${1} nybc-wordpress_db_1:/${1}
docker exec -i nybc-wordpress_db_1 bash -c "mysql -u $DB_USER -p$DB_PASSWORD $DB_NAME < /${1}"
docker exec -it nybc-wordpress_wp_1 wp search-replace ${2} ${3}
