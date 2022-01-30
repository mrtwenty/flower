# FlowerMQ

FlowerMQ 一个基于Workerman和Redis实现的消息队列,一个小小工具，用来给主项目解耦的，也支持延迟队列，失败尝试这些。

## 运行依赖

- php7.2
- Redis版本需要在`5.0.4`上，因为用到Redis的Stream数据类型
- pecl依赖，redis扩展
- composer依赖，workerman/workerman 4.0以上

## 安装

### composer安装

```shell
composer create-project mrtwenty/flower
```

### 下载安装

1. 下载或者 `git clone`
2. 项目根目录执行命令，`composer install`

## 原理说明

1. workerman实现消费端，开多个进程，在 onWorkerStart 函数里面，阻塞读取，阻塞间隔5秒后，就重新阻塞,因为是阻塞了，所以用了一个字符串key来处理停止的问题，每次阻塞5秒，就断开，以便判断是否需要终止程序。
3. 可以随时停止消费端，因为客户端发送的消息都会存放redis stream 队列里面。
4. 一个pending进程,每隔0.5秒检查是否有未ack的消息，并尝试消费掉
5. 一个delay进程,负责处理延迟消息，利用redis的zset有序集合存储，起一个定时器，定时获取可以执行的消息，写入消费端
6. 遵循约定大于配置的方式，直接用默认的即可。
7. 默认配置是app目录下的config目录，如果需要更改配置项，可以在项目根目录下，提供一个.env的配置文件，替换掉
7. 回收裁剪机制: 有三种模式，默认 **no** ，不做裁剪
   1. no,不做裁剪，所有消息保留。
   2. maxlen, 最大长度回收,概率性触发 `xtrim  maxlen mq ~ 长度` 。
   3. minid,    最小已读消息回收，概率行触发，`xtrim minid mq ~ 消息id` ，需要 redis server 6.2.0 以上。


### 可用命令

#### win

windows下仅限于开发，不适合做生产环境使用，启动需要开三个命令行窗口，执行 start、pending、delay命令

1. php index.php start           启动消费队列
2. php index.php pending     启动重试队列
3. php index.php delay          启动延迟队列
4. php index.php test             测试，执行此命令会发送两个消息给服务，一个是即时消息，一个是延迟消息。
5. php monitor.php start      运行信息查看，会启动一个http进程

#### linux

1. php index.php start        linux启动相当于执行了 start、pending、delay命令
1. php index.php start -d   守护进程启动 
2. php index.php stop         强制停止,可能会导致消息未ack，不建议使用
3. php index.php stop -g     优雅停止 (不加参数-g会强制干掉子进程，加参数-g的话，会等子进程处理完后再关闭)
4. php index.php config     查看配置信息
5. php index.php test         测试，执行此命令会发送两个消息给服务，一个是即时消息，一个是延迟消息。
6. php monitor.php start      运行信息查看，会启动一个http进程

#### 服务端说明

1. 下载项目后，配置 .env 
2. 编写业务逻辑，app\consumer\Run.php 只需要编写这里，如果代码有curl请求，记得要做好超时
3. 启动，php index.php start 即可。

### 客户端说明

flower配备了一个客户端，方便在别的项目中使用:

```shell
composer install mrtwenty/flower-client
```

使用方式:

```php
$redis = new Redis;
$redis->connect('127.0.0.1', 6379);
//$mq需要与服务端的配置信息相同
$mq    = ['name' => 'mq','delay_name' => 'mq_delay'];
$client = new Client($redis, $mq);

//立即执行
$res = $client->add(['test' => 'data']);
var_dump($res);
//延迟消息
$res = $client->add(['test' => 'data'], 3);
var_dump($res);
```

### 问题

1. 如何支持两个消息队列，请查看demo1
2. 如何支持一个消息队列，多个消费组, 查看demo2
3. 性能测试，查看demo3
4. 如何操作MySQL,查看demo4
5. 如何避免内存泄露，由于是守护进程，业务代码bug隐藏的内存泄露，可以在消费者执行完一定数量的时候重启进程。具体实现请查看workerman手册。[链接1](https://www.workerman.net/doc/workerman/worker/stop-all.html)、[链接2](https://www.workerman.net/doc/workerman/faq/max-requests.html)
6. 为啥需要Redis 5.0.4 及以上，[5.0.3有个xClaim的bug](https://github.com/redis/redis/commit/f72f4ea311d31f7ce209218a96afb97490971d39),不会触发消息转义加1的问题。
7. 时间不对，因为系统没有设置默认时区，而这个应该开发者确定，可以在入口文件 index.php 和 monitor.php 里面写上:

```php
date_default_timezone_set('PRC');
```


### 相关资料

1. [redis stream 手册](https://redis.io/commands/xack)  是redis stream命令的详细介绍。
2. [redis streams简介](https://redis.io/topics/streams-intro)  是redis官网关于redis stream的介绍，在使用该项目前，建议详细阅读它。
3. [pecl redis 文档](https://github.com/phpredis/phpredis)，  如何使用php操作redis的文档
4. [workerman 手册](https://www.workerman.net/doc/workerman/)

### 引用
1. [monitor登录页模板](https://gitee.com/suiboyu/front_page_effect_collection)
2. env、config类这些学自thinkphp
3. monitor 后端的一些代码，学自webman
4. monitor主页面，用的layui
4. 延迟队列的思路抄的workerman的redis queue
4. 感谢workerman、thinkphp、layui、redis



