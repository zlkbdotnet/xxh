## ZFAKA安装教程（Docker版）：

Docker版优势

**优点**

- 30s即可成功搭建一个zfaka（熟悉docker的人）
- 新手从0开始，也可以快速搭建，全自动部署
- 不用手动去配置yaf、扩展插件、伪静态等等
- 采用前后端数据分离、更安全
**正式商用环境部署**
有时候需要`phpadmin`来管理数据库或使用`kodexplorer`来管理源码文件，这时候需要增加多个容器。因此，本站也做了`docker-compose.yml`来直接启动所有工具，一步到位。

```
wget https://raw.githubusercontent.com/Baiyuetribe/zfaka/docker/docker-compose.yml
docker-compose up -d
```

说明：

- ZFAKA主程序入口为：`http://域名:3002` 打开后填入数据库密码即可完成安装步骤。
- phpadmin入口：`http://域名:8080` 用来修改数据库
- kodexplore入口：`http://域名:999` 用来管理源码或替换图片等等。
## 一键脚本(旧)
```
bash <(curl -L -s https://raw.githubusercontent.com/Baiyuetribe/zfaka/master/zfaka.sh)
```

## 搭建过程截图
#### 脚本首页
![](https://ww1.sinaimg.cn/large/007i4MEmgy1g1z4l0iuxlj30la0flgmb.jpg)

#### 安装设置
只需设置访问的端口号：root数据库密码；选择需要安装的版本即可。
![](https://ww1.sinaimg.cn/large/007i4MEmgy1g1z4my91hwj30ia0cu3za.jpg)

#### 安装成功
![](https://ww1.sinaimg.cn/large/007i4MEmgy1g1z4ozs5bxj30h808pgm0.jpg)

## 手动搭建教程
[【手动搭建】]()

博客：https://baiyue.one 佰阅部落
原作开发者：资料空白
