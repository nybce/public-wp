FROM node:14-alpine as builder
COPY ./site/web/app/themes/boilerplate-theme/package.json /source/
WORKDIR /source
RUN npm install

FROM node:14-alpine

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

RUN yarn config set cache-folder /var/cache/yarn
COPY --from=builder /source/node_modules /theme/node_modules
WORKDIR /theme

COPY docker/bin/theme-entrypoint.sh /usr/local/bin/theme-entrypoint.sh
ENTRYPOINT ["theme-entrypoint.sh"]
CMD ["npm", "run", "start"]
