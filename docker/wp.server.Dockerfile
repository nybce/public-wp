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

# Remove unused dependencies
RUN rm -rf /var/cache/apk/*

RUN mkdir /var/cache/composer
ENV COMPOSER_HOME=/var/cache/composer


# WORDPRESS CLI
RUN curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar
RUN php ./wp-cli.phar --info
RUN chmod +x wp-cli.phar
RUN mv wp-cli.phar /usr/local/bin/wp

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


# Installing Composer
RUN php -r "readfile('http://getcomposer.org/installer');" | php -- --install-dir=/usr/bin/ --filename=composer
RUN alias composer='php /usr/bin/composer'
# Set the user
COPY ./site/composer.json /site
RUN chown -R www-data:www-data /site
USER www-data
RUN ls -ltrah /site
# PHP Composer
RUN mv .env envbak
ARG ACF_PRO_KEY=''
ENV ACF_PRO_KEY ${ACF_PRO_KEY}
RUN export ACF_PRO_KEY=${ACF_PRO_KEY}
COPY docker/bin/composer-install-server.sh /site/composer-install.sh
RUN /site/composer-install.sh && rm /site/composer-install.sh

RUN mv envbak .env

ENTRYPOINT ["wp-entrypoint.sh"]

CMD ["wp", "server", "--docroot=web", "--host=0.0.0.0", "--port=80", "--allow-root"]
