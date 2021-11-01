#!/bin/bash
docker exec -i -t wordpress-boilerplate_wp_1 bash -c "/scripts/fetchDb.sh ${1}"
