# ZFAKA发卡系统(本系统基于yaf+layui开发)

>**郑重申明：本项目为开源程序，仅做技术交流使用**

>演示地址：http://faka.zlkb.net/

>永久免费、绝对开源、没有商业版，不支持特殊定制，欢迎提供各种需求和意见与建议。


# 一、系统介绍
>包含自动/手工发卡功能，有会员中心和后台中心。

1.1 会员模块
* 默认情况下，不支持注册，当然后台可以开放注册；

* 注册成会员可查看历史购买记录。
	
1.2 购买模块
* 支持自动发卡和手工发卡模式；

1.3 后台模块
* 包含订单模块、商品模块、配置模块、卡密导入导出等；
	
1.4 补充关于支付渠道问题
* 支付方式：支付宝当面付，码支付

>不要问我可不可以支持，只要你提供测试账户与接口文档，一般1－2周我都会更新上去；


# 二、系统部署

## [使用docker-compose快速部署(网速快，只需要几分钟)](https://github.com/zsnmwy/zfaka/wiki/%E4%BD%BF%E7%94%A8docker-compose%E5%BF%AB%E9%80%9F%E9%83%A8%E7%BD%B2(%E7%BD%91%E9%80%9F%E5%BF%AB%EF%BC%8C%E5%8F%AA%E9%9C%80%E8%A6%81%E5%87%A0%E5%88%86%E9%92%9F)

## 2.1 环境安装

### 2.1.1 lnmp环境
>参考：[lnmp环境中如何进行配置](https://github.com/zlkbdotnet/zfaka/wiki/lnmp%E7%8E%AF%E5%A2%83%E4%B8%AD%E5%A6%82%E4%BD%95%E8%BF%9B%E8%A1%8C%E9%85%8D%E7%BD%AE).

### 2.1.2 宝塔环境
>参考：[宝塔环境中如何进行配置](https://github.com/zlkbdotnet/zfaka/wiki/%E5%AE%9D%E5%A1%94%E7%8E%AF%E5%A2%83%E4%B8%AD%E5%A6%82%E4%BD%95%E8%BF%9B%E8%A1%8C%E9%85%8D%E7%BD%AE).

### 2.1.3 YAF安装
>参考：[lnmp环境中如何安装yaf](https://github.com/zlkbdotnet/zfaka/wiki/lnmp%E7%8E%AF%E5%A2%83%E4%B8%AD%E5%A6%82%E4%BD%95%E5%AE%89%E8%A3%85yaf).

>参考：[宝塔环境中如何安装yaf](https://github.com/zlkbdotnet/zfaka/wiki/%E5%AE%9D%E5%A1%94%E7%8E%AF%E5%A2%83%E4%B8%AD%E5%A6%82%E4%BD%95%E5%AE%89%E8%A3%85yaf).

### 2.1.4 rewrite配置
>参考：[rewrite配置](https://github.com/zlkbdotnet/zfaka/wiki/rewrite%E9%85%8D%E7%BD%AE).

<pre>#####################################################</pre> 

## 特别补充说明：yaf的环境安装比较麻烦，需要注意一些问题；

* 务必：配置nginx vhost中root路径一定要加上public目录，例如:  /alidata/wwwroot/faka.zlkb.net/public;

* 务必：配置nginx vhost中一定要添加rewrite规则

* 务必：取消防跨站攻击(open_basedir)

* 务必：注意nginx环境下path_info的配置(记的要取消)

* 务必：YAF配置开启命名空间 yaf.use_namespace=1

* 务必：项目运行给站点用户权限

* 建议：mysql数据库配置时建议这样操作参考：https://zlkb.net/302.html

<pre>#######################################################</pre> 

## 2.2 系统安装
>参考：[系统安装指南](https://github.com/zlkbdotnet/zfaka/wiki/%E7%B3%BB%E7%BB%9F%E5%AE%89%E8%A3%85%E6%8C%87%E5%8D%97).

### 2.2.1 下载代码
<pre>
	git clone https://github.com/zlkbdotnet/zfaka.git [这是最新测试版]
</pre> 

>稳定版：请访问这里下载：https://github.com/zlkbdotnet/zfaka/releases

### 2.2.2 修改配置文件名
>新增：需要进入系统conf目录下，application.ini.new修改为 application.ini

### 2.2.3 配置目录权限

* /conf/application.ini 配置文件，可读可写

* /install  安装目录，需要可读写

* /log      日志目录，需要可写

* /temp     缓存目录，需要可读写

### 2.2.4 直接访问安装

### 2.2.5 安装计划任务crontab模块,配置定时计划,用于定时发送邮件
* 常规计划任务crontab的部署,/alidata/wwwroot/faka.zlkb.net/路径请替换成自己的
<pre>
*/2 * * * * php -q /alidata/wwwroot/faka.zlkb.net/public/cli.php request_uri="/crontab/sendemail/index"
</pre> 	

* 宝塔环境计划任务crontab的部署
>参考：[宝塔环境中如何部署计划任务](https://github.com/zlkbdotnet/zfaka/wiki/%E5%AE%9D%E5%A1%94%E7%8E%AF%E5%A2%83%E4%B8%AD%E5%A6%82%E4%BD%95%E9%83%A8%E7%BD%B2%E8%AE%A1%E5%88%92%E4%BB%BB%E5%8A%A1).

### 2.3 系统配置
>参考：[系统配置指南](https://github.com/zlkbdotnet/zfaka/wiki/%E7%B3%BB%E7%BB%9F%E9%85%8D%E7%BD%AE%E6%8C%87%E5%8D%97)

### 2.4 后台安全
>参考： [后台地址安全增强处理](https://github.com/zlkbdotnet/zfaka/wiki/%E5%90%8E%E5%8F%B0%E5%9C%B0%E5%9D%80%E5%AE%89%E5%85%A8%E5%A2%9E%E5%BC%BA%E5%A4%84%E7%90%86)

# 三、系统升级
> 升级时，请直接下载最新代码后直接覆盖，登录后台即可按照提示完成升级；

# 四、BUG与问题反馈
* 相关问题QQ交流群：701035212
   
# 五、推广QQ群
* 全国IDC行业精英群:572511758   
* DirectAdmin用户交流群:337686498

# 六、捐赠&打赏
>捐赠名单：[查看名单](https://github.com/zlkbdotnet/zfaka/wiki/%E6%8D%90%E8%B5%A0%E5%90%8D%E5%8D%95)

![1](https://github.com/zlkbdotnet/zfaka/blob/master/public/res/images/pay/supportme.jpg)
