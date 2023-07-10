#!/usr/bin/env bash
set -euo pipefail

FILENAME=$1
SRC_ENV=$2

echo "Installing dump into database..."
echo "DROP DATABASE db" | mysql --user root --password=database_password --host db
echo "CREATE DATABASE db" | mysql --user root --password=database_password --host db
mysql --user root --password=database_password --host db --database db < "/db_dumps/${FILENAME}"

function rewrite_domain {
    OLD="${1}"
    NEW="${2}"
    wp search-replace --allow-root "${OLD}" "${NEW}" --url="${OLD}" > /dev/null
    wp search-replace --allow-root "https://${NEW}" "http://${NEW}" --url="${NEW}" > /dev/null
}

LOCAL_DOMAINS=($(
cat <<EOF
nybc-enterprise.local.org
ribc.local.org
ctblood.local.org
delmarvablood.local.org
ncbb.local.org
ncbgg.local.org
cbc.local.org
innovativebloodresources.local.org
mbc.local.org
ncbp2.local.org
nybc.local.org
nybcventures.local.org
sharedmedia.local.org
projectachieve.local.org
EOF
))

# Update this list as we transition to real domains
PRODUCTION_DOMAINS=($(
cat <<EOF
www.nybce.org
www.ribc.org
www.ctblood.org
delmarvablood.production.nybc-wordpress.bbox.ly
ncbb.production.nybc-wordpress.bbox.ly
ncbgg.production.nybc-wordpress.bbox.ly
cbc.production.nybc-wordpress.bbox.ly
innovativebloodresources.production.nybc-wordpress.bbox.ly
mbc.production.nybc-wordpress.bbox.ly
ncbp2.production.nybc-wordpress.bbox.ly
nybloodcenter.production.nybc-wordpress.bbox.ly
www.nybcventures.org
sharedmedia.production.nybc-wordpress.bbox.ly
projectachieve.production.nybc-wordpress.bbox.ly
EOF
))

if [[ "${SRC_ENV}" == "production" ]]; then
    echo "Rewriting production urls..."
    for i in ${!PRODUCTION_DOMAINS[@]}; do
        OLD_DOMAIN="${PRODUCTION_DOMAINS[$i]}"
        NEW_DOMAIN="${LOCAL_DOMAINS[$i]}"
        echo "Rewriting ${OLD_DOMAIN} to ${NEW_DOMAIN}"
        rewrite_domain "${OLD_DOMAIN}" "${NEW_DOMAIN}"
    done
fi