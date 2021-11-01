#!/bin/bash
docker exec -i -t wordpress-boilerplate_wp_1 bash -c "/scripts/fetchMedia.sh ${1}"
