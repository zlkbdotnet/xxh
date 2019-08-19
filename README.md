## ZFAKA安装教程（Docker版）：

![](https://img.baiyue.one/upload/2019/08/5d5a09df7a3fa.png)

Docker版优势

**优点**

- 30s即可成功搭建一个zfaka（熟悉docker的人）
- 新手从0开始，也可以快速搭建，全自动部署
- 不用手动去配置yaf、扩展插件、伪静态等等
- 采用前后端数据分离、更安全

## 部署

### 方法1：一键脚本（推荐）

```bash
bash <(curl -L -s https://raw.githubusercontent.com/Baiyuetribe/zfaka/master/zfaka.sh)
```

![](https://img.baiyue.one/upload/2019/07/5d20c37515d89.png)

安装结束后：

![](https://img.baiyue.one/upload/2019/07/5d20c3ac80305.png)



### 方法2：手动部署

请自行搞定docker和docker-compose环境

```
wget https://raw.githubusercontent.com/Baiyuetribe/zfaka/docker/docker-compose.yml
docker-compose up -d
```

说明：

- ZFAKA主程序入口为：`http://域名:3002` 打开后填入数据库密码即可完成安装步骤。
- phpadmin入口：`http://域名:8080` 用来修改数据库
- kodexplore入口：`http://域名:999` 用来管理源码或替换图片等等。

## 安装后相关问题：

![](https://img.baiyue.one/upload/2019/07/5d1c896077502.png)
更多资料，请参考：[【佰阅书籍】](https://book.baiyue.one/document/zfaka/)



博客：https://baiyue.one 佰阅部落
原作开发者：资料空白
