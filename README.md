## ZFAKA安装教程（Docker版）：

Docker版优势

**优点**

- 30s即可成功搭建一个zfaka（熟悉docker的人）
- 新手从0开始，也可以快速搭建，全自动部署
- 不用手动去配置yaf、扩展插件、伪静态等等
- 采用前后端数据分离、更安全

## 安装Docker和Docker-Compose（已安装可跳过）

[docker官方指南](https://docs.docker.com/install/)

[佰阅部落-Docker新手安装教程（含视频教程）](https://baiyue.one/archives/368.html)

## 下载配置文件

```
mkdir -p /opt/zfaka && cd zfaka  #在本地创建zfaka文件，用于数据存储
wget https://github.com/baiyuetribe/zfaka/raw/master/docker-compose.yml   #启动文件
wget https://github.com/baiyuetribe/zfaka/raw/master/.env   #配置文件
```

## 配置文件.env修改

```
MYSQL_ROOT_PASSWORD=baiyue.one
ZFAKA_Version=1.4.0
```

第一个参数可修改默认的ROOT密码；第二个参数为zfaka版本。目前支持zfaka1.3.8及以后的版本。

如不修改默认`.env`，可直接输入

```
docker-compose up -d    #启动
```

如无错误提示，就可以访问https://ip 进入安装后台了。phpadmin地址https://ip:8080。默认root密码`baiyue.one` 。可在.env文件修改。

![](https://ws2.sinaimg.cn/large/007rd8E4ly1g1v826jg6gj30mo05e761.jpg)



## 其他备注

`docker-compose.yml`

```
version: '3.1'
services:
    nginx:
        image: "baiyuetribe/zfaka:nginx1.4.0"
        restart: always
        volumes:
            - /opt/zfaka/upload:/usr/share/nginx/html/public/res/upload       
        ports:
            - "80:80"
        networks:
            - frontend
        depends_on:
            - php
    php:
        image: "baiyuetribe/zfaka:php1.4.0"      
        restart: always
        networks:
            - frontend
            - backend
        depends_on:
            - mysql
    mysql:
        image: mysql:5.7
        volumes:
            - /opt/zfaka/mysql:/var/lib/mysql
        environment:
            TZ: 'Asia/Shanghai'
            MYSQL_ROOT_PASSWORD: baiyue.one
        command: ['mysqld', '--character-set-server=utf8']
        networks:
            - backend

    phpmyadmin:
        image: phpmyadmin/phpmyadmin
        ports:
            - 8080:80
        depends_on:
            - mysql
        environment:
            PMA_HOST: mysql
            TZ: 'Asia/Shanghai'
        networks:
            - backend

networks:
    frontend:
    backend:
```

可以看到，普通用户只能访问前端nginx网络，中间联系层由php联系，后端网络只有mysql和phpadmin和php有权限访问。

## 关于域名访问

**方法一：宝塔反代**
先进入宝塔面板，点击左侧网站，添加站点，完成后进入网站设置，点击反向代理，目标`URL`填入`http://127.0.0.1:代理端口`（*代理端口*就是docker应用的外接接口），再启用反向代理即可。如果想启用`SSL` ，就直接在站点配置即可。

![](https://ww1.sinaimg.cn/large/007i4MEmgy1g04u3wlh5oj30kx0htaci.jpg)

**方法二：caddy反代（没有宝塔时的策略）**

设置较为麻烦，请参考：https://www.moerats.com/archives/422/