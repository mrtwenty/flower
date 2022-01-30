<?php

use app\library\Init;
use app\consumer\Run;

require __DIR__ . '/vendor/autoload.php';

new Init(new Run(), config('redis'), config('flower.mq'));
