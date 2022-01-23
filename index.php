<?php

use app\library\Init;
use app\consumer\Run;

require __DIR__ . '/vendor/autoload.php';

date_default_timezone_set('PRC');

//默认配置 + 公共配置
$flower_config = config('flower.mq') + config('flower_common');

new Init(new Run(), config('redis'), $flower_config);
