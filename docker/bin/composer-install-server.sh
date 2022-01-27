#!/bin/sh
printf "\nInstalling Composer packages\n"
set -a
export COMPOSER_ALLOW_SUPERUSR=1
composer install
