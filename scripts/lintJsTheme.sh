#!/bin/bash
if [[ "${2}" == "fix" ]]; then
    docker-compose exec ${1}-theme bash -c "npm run lint-fix"
else
    docker-compose exec ${1}-theme bash -c "npm run lint"
fi
