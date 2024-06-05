#!/bin/bash
DATE=$(date +"%Y-%m-%d")
set -e

ENV_DIR="env"
ENV_FILE="${ENV_DIR}/${1}.env"
VAULT_PASS_FILE="/site/.vaultpass"

# Ensure the local directory exists
mkdir -p $ENV_DIR

echo "Decrypting environment variables"
# Adjust file permissions to ensure readability inside Docker container
docker exec -i -t --user root wp bash -c "chmod 644 /envs/${1}.env"
docker exec -i -t --user root wp bash -c "chmod 644 $VAULT_PASS_FILE"

# Decrypt the environment variables file inside the Docker container
docker exec -i -t --user root wp bash -c "ansible-vault decrypt /envs/${1}.env --vault-password-file=$VAULT_PASS_FILE"

# Copy the decrypted file to the local machine
docker cp wp:/envs/${1}.env $ENV_FILE

echo "Sourcing environment variables"
# Source the environment variables from the local file
source $ENV_FILE

echo "Reencrypting environment variables"
# Re-encrypt the environment variables file inside the Docker container
docker exec -i -t --user root wp bash -c "ansible-vault encrypt /envs/${1}.env --vault-password-file=$VAULT_PASS_FILE"

# Copy the re-encrypted file back to the local machine (optional, for backup)
docker cp wp:/envs/${1}.env $ENV_FILE

echo "Dumping DB"
# Dump the database
mysqldump -h $DB_HOST -u $DB_USER -p$DB_PASSWORD $DB_NAME > nybc-wp-${1}-to-local-${DATE}.sql

echo "Find/Replace DB"
# Perform find/replace operations on the SQL dump
sed 's#.${1}.nybc-wordpress.bbox.ly#.local.org#g' nybc-wp-${1}-to-local-${DATE}.sql > nybc-wp-${1}-to-local-${DATE}-replaced.sql
sed 's#https://7rcmher38fkopqq0ks.blob.core.windows.net#http://nybc-enterprise.${1}.nybc-wordpress.bbox.ly#g' nybc-wp-${1}-to-local-${DATE}-replaced.sql > nybc-wp-${1}-to-local-${DATE}.sql
sed 's#storage-wordpress-web-${1}#app/uploads#g' nybc-wp-${1}-to-local-${DATE}.sql > nybc-wp-${1}-to-local-${DATE}-replaced.sql

echo "Downloading DB"
# Move the modified SQL dump to the desired location
mv nybc-wp-${1}-to-local-${DATE}-replaced.sql ./db_dumps
