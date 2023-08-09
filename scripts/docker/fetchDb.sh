#!/bin/bash
set -euo pipefail
set +a
source <(ansible-vault view /envs/${1}.env --vault-password-file=/envs/.vaultpass)
set -a
FILENAME="${1}-$(date +"%Y-%m-%d").sql"
echo "Dumping database to /db_dumps/${FILENAME}"
mysqldump --user "${DB_USER}" --password="${DB_PASSWORD}" --host "${DB_HOST}" "${DB_NAME}" > "/db_dumps/${FILENAME}"

unset DB_USER
unset DB_PASSWORD
unset DB_HOST
unset DB_NAME
unset DATABASE_URL

echo "Done!"
echo
echo "To load database, run the following:"
echo "  docker compose exec -u root wp bash /scripts/loadDb.sh ${FILENAME} ${1}"