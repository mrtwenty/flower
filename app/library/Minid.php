<?php

declare(strict_types=1);

namespace app\library;

/**
 * 获取最小已消费Id的类
 */
class Minid
{
    protected $redis;
    protected $redisVersion;
    protected $mq;
    public function __construct($redis, $redis_version, $mq)
    {
        $this->redis = $redis;
        $this->redisVersion = $redis_version;
        $this->mq = $mq;
    }

    /**
     * 执行
     */
    public function gc()
    {
        if (version_compare($this->redisVersion, '6.2.0', '<')) {
            return false;
        }

        $minid = $this->getId();

        if ('0-0' === $minid) {
            return false;
        }
        return $this->redis->rawCommand('xtrim', $this->mq, 'minid', '~', $minid);
    }

    /**
     * 获取最小值
     */
    public function getId()
    {
        $minid_hash   = [];

        $groups = $this->redis->xinfo('GROUPS', $this->mq);

        //取出有pending的组
        $pending = [];
        foreach ($groups as $item) {
            $k = array_sum(explode('-', $item['last-delivered-id']));
            $minid_hash[$k] = $item['last-delivered-id'];
            if ($item['pending'] !== 0) {
                $pending[] = $item['name'];
            }
        }
        //从pending组中获取最小id
        foreach ($pending as $group_name) {
            $temp = $this->redis->xpending($this->mq, $group_name);
            if ($temp[0] > 0) {
                $k = array_sum(explode('-', $temp[1]));
                $minid_hash[$k] = $temp[1]; //最小ID
            }
        }
        return $this->minidCompare($minid_hash);
    }

    /**
     * 比较各个组取出的最小ID ，得到最小值
     *
     * @param array $arr 
     * @return string
     */
    protected function minidCompare($arr)
    {
        if (count($arr) == 1) {
            return array_pop($arr);
        }

        if (krsort($arr, SORT_NUMERIC)) {
            return array_pop($arr);
        }

        return '0-0';
    }
}
