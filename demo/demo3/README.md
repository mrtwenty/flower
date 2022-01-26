# 性能测试

硬件: 在i7-6700, 内存16G 的windows上用VMware 虚拟的一台Linux , 1核内存2G

系统:           CentOS Linux release 7.4.1708

redis版本:  6.0.8

php版本:    7.2.4

redis配置:

```shell
#RDB
save 900 1
save 300 10
save 60 10000
#AOF
appendonly yes
appendfsync everysec
```

## 测试添加消息的速度

```shell
php add.php
```

单进程php写入一百万条消息的时间为: 32.3282秒

## 测试消费消息的速度

```shell
php read.php start
```

### redis

1643183308.3152 - 1643183252.7542 = 55.561秒

### file

1643183465.4675  -  1643183410.1798  = 55.2877秒

### shmop

1643183679.9092 - 1643183637.4621= 42.4471秒

------

说明:  测试结果仅供参考，源码里面有测试文件，可以在自己的机器上跑

