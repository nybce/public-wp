#!/bin/bash
docker exec -i -t nybc-wordpress_wp_1 bash -c "/scripts/fetchDb.sh ${1}"
