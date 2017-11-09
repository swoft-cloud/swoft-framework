<p align="center">
    <a href="https://github.com/stelin/swoft" target="_blank">
        <img src="http://www.stelin.me/assets/img/swoft.png" alt="swoft" />
    </a>
</p>


# 简介
Swoft 是基于 Swoole 2.x 的高性能 PHP 微服务框架，内置 HTTP 服务器，框架全协程实现，性能大大优于传统的 PHP-FPM 模式。

- 基于 Swoole 扩展
- 内置 HTTP 协程服务器
- MVC 分层设计
- 高性能路由
- 全局容器注入
- 高性能 RPC
- 别名机制
- 事件机制
- 国际化(i18n)
- 服务治理熔断、降级、负载、注册与发现
- 连接池 Mysql、Redis、RPC
- 数据库 ORM
- 协程、异步任务投递
- 自定义用户进程
- RPC、Redis、HTTP、Mysql 协程和同步客户端无缝切换
- Inotify 自动 Reload
- 强大的日志系统

# 更新记录

* ......
* 2017-08-15 重构console命令行
* 2017-08-24 重写IOC容器，新增控制器路由注解注册，不再依赖php-di。使用时，重新composer安装
* 2017-08-28 inotify自动reload
* 2017-09-02 别名机制、事件机制、国际化(i18n),命名空间统一大写。
* 2017-09-19 数据库ORM
* 2017-10-24 协程、异步任务投递、自定义用户进程、rpc、redis、http、mysql协程和同步客户端无缝切换、HTTP和RPC服务器分开管理
* 2017-11-01 新增定时任务
* 2017-11-02 重构config配置，新增.env配置环境信息

# 系统架构

<p align="center">
    <a href="https://github.com/stelin/swoft" target="_blank">
        <img src="https://github.com/swoft-cloud/swoft-doc/blob/master/assets/images/architecture.png" alt="swoft" />
    </a>
</p>

# 快速入门
## 文档
[**中文文档1**](http://doc.swoft.org)
[**中文文档2**](https://swoft-cloud.github.io/swoft-doc/)

QQ交流群:548173319

## 环境要求
1. PHP 7.X
2. [Swoole 2.x](https://github.com/swoole/swoole-src/releases), 需开启协程和异步Redis
3. [Hiredis](https://github.com/redis/hiredis/releases)
4. [Composer](https://getcomposer.org/)
5. [Inotify](http://pecl.php.net/package/inotify) (可选)

## 安装

### 手动安装

* Clone 项目
* 安装依赖 `composer install

### Composer 安装

* `composer require swoft/framework dev-master` (未开代理，会有点慢)

### Docker 安装

* Linux: `docker run -p 80:80 swoft/swoft`
* Windows: `winpty docker run -p 80:80 swoft/swoft`

## 配置

* 复制项目根目录的 `.env.example` 并命名为 `.env`
* 更改 `.env` 的服务配置，具体参数说明请参考文档

## 启动

启动服务支持 HTTP 和 TCP 同时启动，在 `.env` 中配置。

**常用命令**

```php
// 启动服务，根据 .env 配置决定是否是守护进程
php bin/swoft start

// 守护进程启动，覆盖 .env 守护进程(DAEMONIZE)的配置
php bin/swoft start -d

// 重启
php bin/swoft restart

// 重新加载
php bin/swoft reload

// 关闭服务
php bin/swoft stop

```


# 开发成员

- [stelin](https://github.com/stelin) (phpcrazy@126.com)
- [inhere](https://github.com/inhere) (in.798@qq.com)
- [ccinn](https://github.com/whiteCcinn) (471113744@qq.com)
- [esion](https://github.com/esion1) (esionwong@126.com)
- [huangzhhui](https://github.com/huangzhhui) (huangzhwork@gmail.com)





