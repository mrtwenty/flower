<?php

use app\library\BaseInterface;

/**
 * 读取测试
 */
class Run implements BaseInterface
{

    protected $start;

    protected function microtimeFloat()
    {
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }
    /**
     *
     * @param mixed $data
     * @param mixed $id
     * @return bool 返回true就会执行ack确认消息已消费
     */
    public function consumer($data, $id): bool
    {
        if ($data['i'] == 1) {
            $time_start = $this->microtimeFloat();
            printf("%.4f秒\n", $time_start);
        }

        if ($data['i'] == 1000000) {
            $time_end = $this->microtimeFloat();
            printf("%.4f秒\n", $time_end);
        }

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
