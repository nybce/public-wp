#!/bin/bash
docker exec -i -t nybc-wordpress_wp_1 bash -c "/scripts/fetchMedia.sh ${1}"
