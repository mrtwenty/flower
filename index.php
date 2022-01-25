<?php

use app\library\Init;
use app\consumer\Run;

require __DIR__ . '/vendor/autoload.php';

date_default_timezone_set('PRC');

new Init(new Run(), config('redis'), config('flower.mq'));
