FROM node:16-buster as node
FROM php:8.1-apache-buster
# シェルを bash に置き換え
SHELL ["/bin/bash", "-oeux", "pipefail", "-c"]

# timezone environment
ENV TZ=Asia/Tokyo \
  # ロケール設定。文字コード問題防止
  LANG=ja_JP.UTF-8 \
  LANGUAGE=ja_JP:ja \
  LC_ALL=ja_JP.UTF-8 \
  # composer environment
  COMPOSER_ALLOW_SUPERUSER=1 \
  COMPOSER_HOME=/composer

# composer install
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN apt-get update && \
    # git: ないと面倒。composer経由で使うことあり \
    # libicu-dev: Laravel に必要な intl 拡張に必要 \
    # libonig-dev: 正規表現の鬼車。PHPそのものに必要 \
    # libzip-dev: ないと面倒。composer経由で使うことあり \
    # unzip: ないと面倒。composer経由で使うことあり \
    # locales: 内部コマンドラインで日本語を使うために必要
    apt-get -y install git libicu-dev libonig-dev libzip-dev unzip locales \
    #  for php-ext-gd
    libfreetype6-dev libjpeg62-turbo-dev libpng-dev && \
    # apt-get update の結果を削除。これをするとイメージが軽くなる
    apt-get clean && \
    rm -rf /var/lib/apt/lists/* && \
    # ロケール設定。文字コード問題防止
    locale-gen ja_JP.UTF-8 && \
    localedef -f UTF-8 -i ja_JP ja_JP.UTF-8 && \
    # gd の設定
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    # php-ext-install
    docker-php-ext-install \
        # imagick用
        gd \
        # Laravel に必要
        intl \
        # 開発の高速化に必要
        opcache \
        # MySQL 用
        pdo_mysql \
        # マルチバイト対応。日本語対応
        mbstring \
        # Zip でファイルをダウンロード等の機能に使う
        zip \
        # tcpdf など高精度な計算が必要なライブラリ中で使用
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

# firebase用。かなり重いライブラリなので firebase を使う案件の時だけコメントアウトを外す
# RUN pecl install grpc

RUN composer config -g process-timeout 3600

ENV APACHE_DOCUMENT_ROOT /work/backend/public
# ドキュメントルートをデフォルトの /var/www/　以下から↑のENVに書き換え
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
