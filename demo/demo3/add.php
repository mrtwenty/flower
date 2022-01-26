<?php

use Flower\Client;

require dirname(dirname(__DIR__)) . '/vendor/autoload.php';

function microtime_float()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}

$redis_config = [
    'host' => '127.0.0.1',
    'auth' => '123456',
    'database' => 0,
    'port' => 6379,
];

$flower = ['name' => 'test'];

$redis = redis($redis_config);
$client   = new Client($redis, $flower);

$time_start = microtime_float();

//一次性添加一百万条消息
for ($i = 1; $i <= 1000000; $i++) {
    $client->add(['i' => $i]);
}

$time_end = microtime_float();

$time = $time_end - $time_start;
printf("%.4f秒\n", $time);
