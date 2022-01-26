<?php

use app\library\BaseInterface;

/**
 * 发短信
 */
class Sms implements BaseInterface
{
    /**
     * 发短信
     *
     * @param mixed $data
     * @param mixed $id
     * @return bool 返回true就会执行ack确认消息已消费
     */
    public function consumer($data, $id): bool
    {
        print_r($data);

        return true;
    }

    /**
     * 超过尝试的次数，就会写入失败队列里面，并调用此方法，可以用此方法通知运维
     *
     * @return void
     */
    public function fail($data, $id)
    {
        print_r($data);
        print_r($id);
    }
}
