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
  python2 \
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
WORKDIR /theme

COPY docker/bin/theme-entrypoint.sh /usr/local/bin/theme-entrypoint.sh
ENTRYPOINT ["theme-entrypoint.sh"]
CMD ["npm", "run", "start"]
