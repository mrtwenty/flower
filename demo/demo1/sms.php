<?php

use app\library\Init;

require dirname(dirname(__DIR__)) . '/vendor/autoload.php';
require __DIR__ . '/consumer/Sms.php';

date_default_timezone_set('PRC');

$redis_config = [
    'host'     => '127.0.0.1',
    'auth'     => '123456',
    'database' => 0,
    'port'     => 6379,
];

$flower = [
    'name'       => 'sms', # 消息队列名
    'status'     => 'sms_status', # 消息队列状态
    'fail_list'  => 'sms_fail_list', # 尝试多次后,记录到失败的队列
    'delay_name' => false, # 延迟队列关闭
    'group_name' => 'sms_group', # 消费组名
];

new Init(new Sms(), $redis_config, $flower);
