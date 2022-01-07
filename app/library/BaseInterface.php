<?php

declare(strict_types=1);

namespace app\library;

/**
 * 消费接口类
 */
interface BaseInterface
{
    /**
     * 消费
     *
     * @param mixed $data
     * @param mixed $id
     * @return bool 返回true就会执行ack确认消息已消费
     */
    public function consumer($data, $id): bool;

    /**
     * 失败调用此方法
     *
     * @param mixed $data
     * @return void
     */
    public function fail($data, $id);
}
