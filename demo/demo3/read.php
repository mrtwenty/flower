<?php

use app\library\Init;

require dirname(dirname(__DIR__)) . '/vendor/autoload.php';
require __DIR__ . '/Run.php';


$redis_config = [
    'host' => '127.0.0.1',
    'auth' => '123456',
    'database' => 0,
    'port' => 6379,
];

$flower = [
    'name'            => 'test',             # 消息队列名
    'status'          => 'test_status',      # 消息队列状态
    'fail_list'       => 'test_fail_list',   # 尝试多次后,记录到失败的队列
    'delay_name'      => false,              # 延迟队列关闭
    'group_name'      => 'test',             # 消费组名
    'status_driver'   => 'shmop',             # 测试不同的状态驱动的性能区别: file,redis,shmop
];

new Init(new Run(), $redis_config, $flower);
