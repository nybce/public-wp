FROM php:7.4-apache


RUN apt-get update && \
  apt-get install -y bash \
  ca-certificates \
  curl \
  nano


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

WORKDIR /var/www/html
RUN apt-get install -y ansible
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

RUN mkdir /scripts
COPY ./scripts/docker/ /scripts
RUN mkdir /db_dumps
RUN ["chmod", "+x", "/scripts/fetchDb.sh"]
RUN ["chmod", "+x", "/scripts/fetchMedia.sh"]
COPY ./.env/local.env /var/www/html/.env
COPY ./.env/local.env /.env
RUN mkdir /envs
COPY ./.env/* /envs
COPY .vaultpass /envs

# Update composer dependencies at runtime
COPY docker/bin/wp-entrypoint.sh /usr/local/bin/wp-entrypoint.sh
ENTRYPOINT ["wp-entrypoint.sh"]
RUN a2enmod rewrite expires ssl

WORKDIR /var/www/html

RUN mkdir /etc/apache2/ssl


COPY ./site/config/wp-base.conf /etc/apache2/sites-available/wp-base.conf

RUN a2dissite 000-default && a2ensite wp-base

CMD ["apache2-foreground"]
