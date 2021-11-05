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
COPY ./.env/local.env /site/.env
COPY ./.env/local.env /.env
RUN mkdir /envs
COPY ./.env/* /envs
COPY .vaultpass /envs

# Update composer dependencies at runtime
COPY docker/bin/wp-entrypoint.sh /usr/local/bin/wp-entrypoint.sh
ENTRYPOINT ["wp-entrypoint.sh"]
CMD ["wp", "server", "--docroot=web", "--host=0.0.0.0", "--allow-root"]
