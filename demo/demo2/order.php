<?php

use Flower\Client;

require dirname(dirname(__DIR__)) . '/vendor/autoload.php';


date_default_timezone_set('PRC');

$redis_config = [
    'host' => '127.0.0.1',
    'auth' => '123456',
    'database' => 0,
    'port' => 6379,
];

$flower = [
    'name' => 'order'
];

$redis = redis($redis_config);
$client   = new Client($redis, $flower);

$client->add("test order message");
