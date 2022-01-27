#!/bin/sh
printf "\nInstalling Composer packages\n"
set -a
source /site/.env
composer install
