#! /bin/bash
set -euo pipefail

curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.38.0/install.sh | bash
export NVM_DIR="$HOME/.nvm"
[ -s "$NVM_DIR/nvm.sh" ] && \. "$NVM_DIR/nvm.sh"  # This loads nvm
[ -s "$NVM_DIR/bash_completion" ] && \. "$NVM_DIR/bash_completion"  # This loads nvm bash_completion
nvm install 14
nvm use 14


npm install --prefix=${TUGBOAT_ROOT}/site/web/app/themes/nybc-theme
npm run-script build --prefix=${TUGBOAT_ROOT}/site/web/app/themes/nybc-theme
