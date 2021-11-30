#!/bin/bash
if [[ "${1}" == "fix" ]]; then
    docker-compose exec division-theme bash -c "cd /html/scss/ && ../../theme/node_modules/stylelint/bin/stylelint.js . --config /theme/.stylelintrc --fix"
else
    docker-compose exec division-theme bash -c "cd /html/scss && ../../theme/node_modules/stylelint/bin/stylelint.js . --config /theme/.stylelintrc"
fi
