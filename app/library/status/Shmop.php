<?php

declare(strict_types=1);

namespace app\library\status;

/**
 * 共享内存, start、stop、status
 */
class Shmop implements StatusInterface
{
    protected $shmid;

    public function __construct($key = null, $size = 5)
    {
        //判断某个扩展名是否在其中
        if (!extension_loaded('shmop')) {
            throw new \Exception("shmop extension no found", 1);
        }

        if (empty($key)) {
            $key = ftok(__FILE__, 'a');
        }

        //申请$size字节共享内存空间
        $shm_id = shmop_open($key, "c", 0644, $size);
        if (!$shm_id) {
            throw new \Exception("shmop open error", 1);
        }

        $this->shmid = $shm_id;
    }

    public function start()
    {
        //如果写入失败
        if (!shmop_write($this->shmid, 'start', 0)) {
            throw new \Exception("shmop write error", 1);
        }
        return true;
    }

    public function stop()
    {
        //如果写入失败
        if (!shmop_write($this->shmid, str_pad('stop', 6), 0)) {
            throw new \Exception("shmop write error", 1);
        }
        return true;
    }

    public function status(): string
    {
        if ($status = trim(shmop_read($this->shmid, 0, shmop_size($this->shmid)))) {
            return $status;
        }
        return "stop";
    }
}
