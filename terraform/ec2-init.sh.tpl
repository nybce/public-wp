#!/bin/bash

# create swap file
fallocate -l 1G /swapfile; \
  chmod 600 /swapfile; \
  mkswap /swapfile; \
  swapon /swapfile

# install docker, as i can't manage to use custom image for docker on ubuntu
apt-get update -qq >/dev/null && \
  DEBIAN_FRONTEND=noninteractive apt-get install -y -qq apt-transport-https ca-certificates curl gnupg-agent software-properties-common && \
  curl -fsSL https://download.docker.com/linux/ubuntu/gpg | apt-key add - && \
  add-apt-repository "deb [arch=amd64] https://download.docker.com/linux/ubuntu $(lsb_release -cs) stable" && \
  apt-get update -qq >/dev/null && \
  DEBIAN_FRONTEND=noninteractive apt-get install -y -qq docker-ce docker-ce-cli containerd.io && \
  systemctl enable docker

docker login -u "${docker_registry_username}" -p "${docker_registry_password}" "${docker_registry_host}"


docker pull index.docker.io/nybcteam/nybc-wordpress:${docker_image_tag}
docker run --name nybc-web --restart unless-stopped --net=host \
    -e "ENVIRONMENT=${environment}" \
    -e "ANSIBLE_VAULT_PASS=${ansible_vault_pass}" \
    -d index.docker.io/nybcteam/nybc-wordpress:${docker_image_tag}

docker pull index.docker.io/containrrr/watchtower
docker run --name watchtower --restart unless-stopped \
    -v /root/.docker/config.json:/config.json \
    -v /var/run/docker.sock:/var/run/docker.sock \
    -d index.docker.io/containrrr/watchtower --cleanup
