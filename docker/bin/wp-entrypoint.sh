#!/bin/bash
set -a
if [ -z ${ENVIRONMENT+x} ]; then
    echo "ENVIRONMENT not set"

    if [ -z ${IGNORE_ENVIRONMENT_FAIL+x} ]; then # Start should fail unless IGNORE_ENVIRONMENT_FAIL is set
        exit 2;
    fi

else
    if [ ! -f /envs/$ENVIRONMENT.env ]; then
        ln -fs /envs/local.env /.env
        ln -fs /envs/local.env /site/.env
    else
        ln -fs /envs/$ENVIRONMENT.env /.env
        ln -fs /envs/$ENVIRONMENT.env /site/.env
    fi

    if [ -z ${ENVIRONMENT_UNENCRYPTED+x} ]; then # Decrypt environment file unless $ENVIRONMENT_UNENCRYPTED is set
      head -1 /.env | grep -q \$ANSIBLE_VAULT && ansible-vault decrypt /.env --vault-password-file=/app/scripts/echo_ansible_vault_pass
    fi

    set -a
      source /.env
    set +a

    export $(cut -d= -f1 /.env)
fi

echo "upgrade composer dependencies"
composer update -vvv
# compose update
exec "$@"
