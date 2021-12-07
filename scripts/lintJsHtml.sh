#!/bin/bash
if [[ "${1}" == "fix" ]]; then
    docker-compose exec theme bash -c "npm run lint-html-fix"
else
    docker-compose exec theme bash -c "npm run lint-html"
fi
