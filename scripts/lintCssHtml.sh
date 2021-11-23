#!/bin/bash
if [[ "${1}" == "fix" ]]; then
    docker-compose exec division-theme bash -c "cd /html && ../theme/node_modules/stylelint/bin/stylelint.js **/*.scss --fix"
else
    docker-compose exec division-theme bash -c "cd /html && ../theme/node_modules/stylelint/bin/stylelint.js **/*.scss"
fi
