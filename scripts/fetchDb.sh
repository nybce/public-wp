#!/bin/bash
docker exec -i -t utenn-wordpress_wp_1 bash -c "/scripts/fetchDb.sh ${1}"
