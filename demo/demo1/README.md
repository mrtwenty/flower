# Demo1
flower项目实现两个消息队列，每个队列各自一个消费组，互不干扰

```shell
# 创建两个消费组
php email.php start
php sms.php start

# 向两个队列各自发送一条消息
php add.php
```

