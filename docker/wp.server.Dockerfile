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

FROM php:7.4.27-apache-bullseye


RUN apt-get update
RUN apt-get install -y libmagickwand-dev libzip-dev
        # Install the PHP extensions
RUN docker-php-ext-install mysqli exif zip
RUN pecl install imagick-beta -y
RUN docker-php-ext-enable imagick
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer1 --version=1.10.23
        # Install wp-cli
RUN curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar
RUN chmod +x wp-cli.phar
RUN mv wp-cli.phar /usr/local/bin/wp
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
RUN apt-get install -y ansible
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

RUN mkdir /scripts
COPY ./scripts/docker/ /scripts
RUN ls -al
RUN mkdir /db_dumps
COPY ./.env/dev.env /site/.env
COPY ./.env/dev.env /.env
RUN mkdir /envs
COPY ./.env/* /envs
COPY ./uploads.ini /usr/local/etc/php/conf.d/uploads.ini
# Update composer dependencies at runtime
RUN apt-get install -y libpng-dev unzip
RUN docker-php-ext-install gd


# Installing Composer
RUN php -r "readfile('http://getcomposer.org/installer');" | php -- --install-dir=/usr/bin/ --filename=composer
RUN alias composer='php /usr/bin/composer'
# Set the user

COPY ./site /site
COPY --from=theme-builder /theme /site/web/app/themes/nybc-theme
# PHP Composer
ARG ACF_PRO_KEY=''
ENV ACF_PRO_KEY ${ACF_PRO_KEY}
ARG ENVIRONMENT=''
ENV ENVIRONMENT ${ENVIRONMENT}
ARG COMPOSER_ALLOW_SUPERUSR=1
ENV COMPOSER_ALLOW_SUPERUSR 1
RUN mv /site/.env /site/envbak
RUN composer install
COPY .env/dev.env /site/.env
RUN rm -r /var/www/html
RUN ln -snf /site/web /var/www/html
RUN --mount=type=secret,id=vaultpass \
  cat /run/secrets/vaultpass >> /.vaultpass
RUN chmod 666 /.vaultpass
COPY docker/bin/wp-server-entrypoint.sh /usr/local/bin/wp-entrypoint.sh
RUN /usr/local/bin/wp-entrypoint.sh
ENTRYPOINT ["docker-php-entrypoint"]
RUN ln -s /etc/apache2/mods-available/rewrite.load /etc/apache2/mods-enabled/rewrite.load

WORKDIR /var/www/html

EXPOSE 80
CMD ["apache2-foreground"]