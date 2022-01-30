<?php

use app\library\Init;


require dirname(dirname(__DIR__)) . '/vendor/autoload.php';
require __DIR__ . '/consumer/Integral.php';

$redis_config = [
    'host' => '127.0.0.1',
    'auth' => '123456',
    'database' => 0,
    'port' => 6379,
];


$flower = [
    'name'            => 'order',              # 消息队列名
    'status'          => 'integral_status',    # 消息队列状态
    'fail_list'       => 'integral_fail_list', # 尝试多次后,记录到失败的队列
    'delay_name'      => false,                # 延迟队列关闭
    'group_name'      => 'integral',           # 消费组名
];

new Init(new Integral(), $redis_config, $flower);
