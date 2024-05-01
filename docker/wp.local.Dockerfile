FROM php:7.4-fpm-alpine


ENV COMPOSER_HOME=/var/cache/composer
ARG YOAST_SEO_KEY
ARG ACF_PRO_KEY

COPY ./.vaultpass /envs/.vaultpass
COPY ./env/* /envs/
COPY ./env/local.env /.env
COPY ./env/local.env /site/.env
COPY ./scripts/docker/ /scripts
COPY ./site/composer.json /site
COPY ./docker/bin/composer-install.sh /site/composer-install.sh

RUN apk upgrade \
# Install deps
  && apk add --no-cache \
  ansible \
  bash \
  ca-certificates \
  curl \
  libc6-compat \
  libpng \
  libpng-dev \
  mysql-client \
  nano \
  openssh \
  tar \
  wget \
  && rm -rf /var/cache/apk/* \
# Get azcopy
  && wget -O /tmp/azcopy.tar https://aka.ms/downloadazcopy-v10-linux \
  && tar -C /tmp -xf /tmp/azcopy.tar --transform 's!^[^/]*!azcopy!' \
  && mv /tmp/azcopy/azcopy /usr/bin/azcopy \
  && rm -r /tmp/azcopy && rm -r /tmp/azcopy.tar \
  && mkdir /var/cache/composer \
# WORDPRESS CLI
  && wget -O /usr/local/bin/wp https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar \
  && chmod +x /usr/local/bin/wp \
# PHP extensions
  && docker-php-ext-install mysqli && docker-php-ext-enable mysqli \
  && docker-php-ext-install gd && docker-php-ext-enable gd \
# Install Composer
  && php -r "readfile('http://getcomposer.org/installer');" | php -- --install-dir=/usr/bin/ --filename=composer \
  && alias composer='php /usr/bin/composer'

RUN mkdir -p /db_dumps \
  && chmod +x /scripts/fetchDb.sh \
  && chmod +x /scripts/fetchMedia.sh \
  && chmod -R 777 /var/cache/composer \
  && chown www-data:www-data /site

WORKDIR /site

# Set the user
USER www-data

# Set the COMPOSER_AUTH environment variable
ENV COMPOSER_AUTH="{\"http-basic\": {\"connect.advancedcustomfields.com\": {\"username\": \"${ACF_PRO_KEY}\", \"password\": \"https://www.nybce.org/\"}}}"

# PHP Composer
RUN composer config -g http-basic.my.yoast.com token ${YOAST_SEO_KEY} && /site/composer-install.sh && rm /site/composer-install.sh

COPY docker/bin/wp-entrypoint.sh /usr/local/bin/wp-entrypoint.sh
ENTRYPOINT ["wp-entrypoint.sh"]
CMD ["wp", "server", "--docroot=web", "--host=0.0.0.0", "--allow-root"]
