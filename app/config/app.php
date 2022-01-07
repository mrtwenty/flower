<?php

return [
    'mysql' => [
        'host' => env('mysql.host', '127.0.0.1'),
        'username' => env('mysql.username', 'root'),
        'password' => env('mysql.password', '123456'),
        'database' => env('mysql.database', 'test'),
        'port' => (int)env('mysql.port', 3306),
    ],
    'redis' => [
        'host' => env('redis.host', '127.0.0.1'),
        'auth' => env('redis.auth', '123456'),
        'database' => (int)env('redis.database', 0),
        'port' => (int)env('redis.port', 3306),
    ],
    'monitor' => [
        'socket'   => env('monitor.socket', 'http://0.0.0.0:8080'),
        'username' => env('monitor.username', 'admin'),
        'password' => env('monitor.password', '123456'),
    ],
    'flower' => [
        'mq' => [
            'name'            => env('mq.name', 'mq'),                # 消息队列名
            'status'          => env('mq.status', 'mq_status'),       # 消息队列状态 
            'fail_list'       => env('mq.fail_list', 'mq_fail_list'), # 尝试多次后,记录到失败的队列
            'delay_name'      => env('mq.delay_name', 'mq_delay'),    # 延迟队列名
            'group_name'      => env('mq.group_name', 'mq_group'),    # 消费组名
            'consumer_num'    => (int)env('mq.consumer_num', 8),      # 消费进程
            'try_fail_num'    => (int)env('mq.try_fail_num', 3),      # 失败尝试次数
            'try_fail_second' => (int)env('mq.try_fail_second', 6),   # 失败后隔多少秒重试
            'maxlen'          => (int)env('mq.maxlen', 100000),        # 最大队列长度
            'gc_probability'  => (int)env('mq.gc_probability', 1),    # gc_probability/gc_divisor 概率
            'gc_divisor'      => (int)env('mq.gc_divisor', 10000),
        ],
    ],
];
