FROM node:14-alpine as node-base

RUN apk add --no-cache bash \
  autoconf \
  automake \
  make \
  g++ \
  libtool \
  gifsicle \
  libjpeg-turbo-utils \
  libpng-dev \
  libjpeg-turbo \
  libjpeg-turbo-dev \
  libpng \
  libpng-dev \
  libwebp \
  libwebp-dev \
  nasm \
  zlib \
  zlib-dev \
  lcms2-dev
RUN rm -rf /var/cache/apk/*

FROM node-base as theme-builder
COPY ./site/web/app/themes/nybc-theme/package.json /source/
WORKDIR /source
RUN npm install
RUN mkdir /theme
RUN mv /source/node_modules /theme/node_modules
COPY ./site/web/app/themes/nybc-theme /theme
WORKDIR /theme
RUN npm run build

FROM php:7.4-fpm-alpine


RUN apk upgrade && \
  apk add --no-cache bash \
  ca-certificates \
  curl \
  mysql-client


# Install XDebug
#RUN pecl config-set php_ini /etc/php7/php.ini
#RUN pecl install xdebug
#RUN echo 'zend_extension=/usr/lib/php7/modules/xdebug.so' >> /etc/php7/php.ini
#RUN touch /etc/php7/conf.d/xdebug.ini
#RUN echo 'xdebug.remote_enable = 1' >> /etc/php7/conf.d/xdebug.ini
#RUN echo 'xdebug.remote_autostart = 1' >> /etc/php7/conf.d/xdebug.ini
#RUN echo 'xdebug.remote_connect_back = 1' >> /etc/php7/conf.d/xdebug.ini
#RUN echo 'xdebug.remote_handler = dbgp' >> /etc/php7/conf.d/xdebug.ini
#RUN echo 'xdebug.profiler_enable = 1' >> /etc/php7/conf.d/xdebug.ini
#RUN echo 'xdebug.profiler_output_dir = "/data/web"' >> /etc/php7/conf.d/xdebug.ini
#RUN echo 'xdebug.remote_port = 9000' >> /etc/php7/conf.d/xdebug.ini

# Remove unused dependencies
RUN rm -rf /var/cache/apk/*

RUN mkdir /var/cache/composer
ENV COMPOSER_HOME=/var/cache/composer


# WORDPRESS CLI
RUN curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar
RUN php ./wp-cli.phar --info
RUN chmod +x wp-cli.phar
RUN mv wp-cli.phar /usr/local/bin/wp

# PHP Composer
COPY docker/bin/composer-install.sh /tmp/composer-install.sh
RUN /tmp/composer-install.sh

WORKDIR /site
RUN apk add ansible
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

RUN mkdir /scripts
COPY ./scripts/docker/ /scripts
RUN ls -al
RUN mkdir /db_dumps
RUN apk add openssh
RUN ["chmod", "+x", "/scripts/fetchDb.sh"]
RUN ["chmod", "+x", "/scripts/fetchMedia.sh"]
COPY ./.env/dev.env /site/.env
COPY ./.env/dev.env /.env
RUN mkdir /envs
COPY ./.env/* /envs
COPY ./scripts/echo_ansible_vault_pass.sh /echo_ansible_vault_pass.sh
COPY ./site /site
COPY ./uploads.ini /usr/local/etc/php/conf.d/uploads.ini
# Update composer dependencies at runtime
COPY docker/bin/wp-server-entrypoint.sh /usr/local/bin/wp-entrypoint.sh
COPY --from=theme-builder /theme /site/web/app/themes/nybc-theme
RUN apk add --no-cache libpng libpng-dev && docker-php-ext-install gd && apk del libpng-dev
COPY ./.vaultpass /vaultpass
RUN cat /vaultpass >> /.vaultpass-test
RUN chmod 666 /.vaultpass-test
ENTRYPOINT ["wp-entrypoint.sh"]
CMD ["wp", "server", "--docroot=web", "--host=0.0.0.0", "--port=9000", "--allow-root"]
