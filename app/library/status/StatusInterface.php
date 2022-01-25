<?php

declare(strict_types=1);

namespace app\library\status;

/**
 * 运行状态接口
 */
interface StatusInterface
{
    public function start();

    public function stop();

    public function status(): string;
}
