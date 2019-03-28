FROM newfuture/yaf:php7-1.1

MAINTAINER azure <baiyue.one>

ENV TZ=Asia/Shanghai


COPY ./php.ini /usr/local/etc/php/

COPY ./php.conf /usr/local/etc/php/conf.d/php.conf

COPY ./ /usr/share/nginx/html

RUN chmod a+w /usr/share/nginx/html/conf/application.ini && \
    chmod -R a+w+r /usr/share/nginx/html/install/ && \
    chmod -R a+w+r /usr/share/nginx/html/temp && \
    chmod -R a+w /usr/share/nginx/html/log

