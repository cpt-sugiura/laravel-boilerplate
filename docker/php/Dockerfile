FROM node:16-buster as node
FROM php:8.1-apache-buster
SHELL ["/bin/bash", "-oeux", "pipefail", "-c"]

# timezone environment
ENV TZ=Asia/Tokyo \
  # composer environment
  COMPOSER_ALLOW_SUPERUSER=1 \
  COMPOSER_HOME=/composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN apt-get update && \
  apt-get -y install git libicu-dev libonig-dev libzip-dev unzip locales \
  #  for php-ext-gd
  libfreetype6-dev libjpeg62-turbo-dev libpng-dev && \
  apt-get clean && \
  rm -rf /var/lib/apt/lists/* && \
  locale-gen en_US.UTF-8 && \
  localedef -f UTF-8 -i en_US en_US.UTF-8 && \
  mkdir /var/run/php-fpm
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
# php-ext-install
RUN docker-php-ext-install \
        gd \
        intl \
        opcache \
        pdo_mysql \
        mbstring \
        zip \
        bcmath
# for php-ext-imagick
RUN apt-get update && apt-get -y install --no-install-recommends libmagickwand-dev imagemagick \
  && mkdir /tmp/imagick \
  && cd /tmp/imagick \
  && git clone https://github.com/Imagick/imagick .\
  && phpize && ./configure \
  && make \
  && make install \
  && rm -rf /tmp/imagick \
  && docker-php-ext-enable imagick

RUN composer config -g process-timeout 3600

ENV APACHE_DOCUMENT_ROOT /work/backend/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
 && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf \
 && a2enmod rewrite

COPY ./php.ini /usr/local/etc/php/php.ini

# node command
COPY --from=node /usr/local/bin /usr/local/bin
# npm command
COPY --from=node /usr/local/lib /usr/local/lib
# yarn command
COPY --from=node /opt /opt

WORKDIR /work/backend
