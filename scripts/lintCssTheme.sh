#!/bin/bash
if [[ "${2}" == "fix" ]]; then
    docker-compose exec theme bash -c "npm run lint-css-fix"
else
    docker-compose exec theme bash -c "npm run lint-css"
fi
