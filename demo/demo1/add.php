<?php

use Flower\Client;

require dirname(dirname(__DIR__)) . '/vendor/autoload.php';

$redis_config = [
    'host' => '127.0.0.1',
    'auth'     => '123456',
    'database' => 0,
    'port'     => 6379,
];

// sms add
$flower = ['name' => 'sms'];
$redis  = redis($redis_config);
$client = new Client($redis, $flower);
$client->add("test sms message");

//email add
$flower = ['name' => 'email'];
$redis = redis($redis_config);
$client   = new Client($redis, $flower);
$client->add("test email message");
