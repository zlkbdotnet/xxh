FROM php:7.0-fpm-jessie

MAINTAINER zsnmwy <szlszl35622@gmail.com>

ENV TZ=Asia/Shanghai

COPY sources.list /etc/apt/sources.list

RUN set -xe \
    && echo "构建依赖" \
    && buildDeps=" \
        build-essential \
        dh-php5 \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libmcrypt-dev \
        libpng12-dev \
        wget \
    " \
    && echo "运行依赖" \
    && runtimeDeps=" \
        libfreetype6 \
        libjpeg62-turbo \
        libmcrypt4 \
        libpng12-0 \
    " \
    && echo "安装 php 以及编译构建组件所需包" \
    && DEBIAN_FRONTEND=noninteractive \
    && apt-get update \
    && apt-get install -y ${runtimeDeps} ${buildDeps} --no-install-recommends \
    && echo "编译安装 php 组件" \
    && docker-php-ext-install iconv mcrypt mysqli pdo pdo_mysql zip \
    && docker-php-ext-configure gd \
        --with-freetype-dir=/usr/include/ \
        --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install gd \
    && echo "编译安装 yaf" \
    && wget -c http://pecl.php.net/get/yaf-3.0.2.tgz \
    && tar zxf yaf-3.0.2.tgz \
    && cd yaf-3.0.2/ \
    && phpize \
    && ./configure --with-php-config=/usr/local/bin/php-config \
    && make \
    && make install \
    && make clean \
    && DEBIAN_FRONTEND=noninteractive apt-get install cron -y \
    && echo "清理" \
    && apt-get purge -y --auto-remove \
        -o APT::AutoRemove::RecommendsImportant=false \
        -o APT::AutoRemove::SuggestsImportant=false \
        $buildDeps \
    && rm -rf /var/cache/apt/* \
    && rm -rf /var/lib/apt/lists/*

COPY ./php.ini /usr/local/etc/php/

COPY ./php.conf /usr/local/etc/php/conf.d/php.conf

COPY ./site /usr/share/nginx/html

RUN chmod a+w /usr/share/nginx/html/conf/application.ini && \
    chmod -R a+w+r /usr/share/nginx/html/install/ && \
    chmod -R a+w+r /usr/share/nginx/html/temp && \
    chmod -R a+w /usr/share/nginx/html/log

RUN (crontab -l 2>/dev/null; echo '*/2 * * * * php -q /usr/share/nginx/html/public/cli.php request_uri="/crontab/sendemail/index"') | crontab -
