<?php

declare(strict_types=1);

namespace app\library;

use app\library\status\Redis;
use app\library\status\Shmop;
use app\library\status\File;

class Status
{
    protected $status;

    public function __construct($mq, $redis_config, $key = 'master')
    {
        //标识启动
        if ('redis' === $mq['status_driver']) {
            $redis = redis($redis_config, $key);
            $this->status = new Redis($redis, $mq['status']);
            return;
        }

        if ('shmop' === $mq['status_driver']) {
            $this->status = new Shmop();
            return;
        }

        if ('file' === $mq['status_driver']) {
            $this->status = new File($mq['status']);
            return;
        }

        throw new \Exception("status driver not found", 1);
    }

    public function start()
    {
        $this->status->start();
    }

    public function stop()
    {
        $this->status->stop();
    }

    public function status()
    {
        return $this->status->status();
    }
}
