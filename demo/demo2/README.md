# Demo2
实现同一个消息队列order，两个消费组, integral积分和coupon优惠券
即广播功能，一条消息，同时被两个消费组消费

```shell
# 创建两个消费组
php coupon.php start
php integral.php start

# 发消息
php order.php
```