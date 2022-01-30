<?php

use app\library\Init;


require dirname(dirname(__DIR__)) . '/vendor/autoload.php';
require __DIR__ . '/consumer/Email.php';

$redis_config = [
    'host' => '127.0.0.1',
    'auth' => '123456',
    'database' => 0,
    'port' => 6379,
];

$flower = [
    'name'            => 'email',             # 消息队列名
    'status'          => 'email_status',      # 消息队列状态
    'fail_list'       => 'email_fail_list',   # 尝试多次后,记录到失败的队列
    'delay_name'      => false,               # 延迟队列关闭
    'group_name'      => 'email_group',       # 消费组名
];

new Init(new Email(), $redis_config, $flower);
