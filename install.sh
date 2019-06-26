#!/bin/bash

#匹配php环境
siteName=$1
PHP_V=$(cat /www/server/panel/vhost/nginx/${siteName}.conf |grep enable-php|tr -cd '[0-9]')

# 设置yaf扩展
phpPath=/www/server/php/$PHP_V/etc/php.ini
is_yaf=$(cat $phpPath|grep 'yaf.use_namespace=1')
if [ "$is_yaf" = "" ];then
	echo "[yaf]" >> $phpPath
	echo "yaf.use_namespace=1" >> $phpPath
	/etc/init.d/php-fpm-$PHP_V reload
fi

# 设置pathinfo
enable_php=/www/server/nginx/conf/enable-php-$PHP_V.conf
if [ -f $enable_php ];then
	 sed -i 's/include pathinfo.conf/#include pathinfo.conf/g' $enable_php
fi
