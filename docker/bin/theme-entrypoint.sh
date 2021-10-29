#!/bin/bash
# rm -rf node_modules/
# ls -al node_modules/
ls -ltrh /theme
#npm rebuild node-sass
npm run start
ls -lrth
exec "$@"
