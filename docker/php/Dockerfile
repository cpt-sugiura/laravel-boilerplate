FROM php:8.0-fpm-buster
SHELL ["/bin/bash", "-oeux", "pipefail", "-c"]

# timezone environment
ENV TZ=JST \
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
RUN  docker-php-ext-configure gd --with-freetype --with-jpeg
# php-ext-install
RUN docker-php-ext-install \
        gd \
        intl \
        opcache \
        pdo_mysql \
        mbstring \
        zip \
        bcmath
# for firebase
RUN pecl install grpc
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

RUN  composer config -g process-timeout 3600

COPY ./php-fpm.d/zzz-www.conf /usr/local/etc/php-fpm.d/zzz-www.conf
COPY ./php.ini /usr/local/etc/php/php.ini

WORKDIR /work/backend
