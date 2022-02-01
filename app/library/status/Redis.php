<?php

declare(strict_types=1);

namespace app\library\status;

/**
 * redis key标识
 */
class Redis implements StatusInterface
{
    protected $redis;

    protected $key;

    public function __construct($redis, $key)
    {
        $this->redis = $redis;
        $this->key   = $key;
    }

    public function start()
    {
        return $this->redis->set($this->key, 'start');
    }

    public function stop()
    {
        return $this->redis->set($this->key, 'stop');
    }

    public function status(): string
    {
        if ($status = $this->redis->get($this->key)) {
            return $status;
        }
        return "stop";
    }
}
