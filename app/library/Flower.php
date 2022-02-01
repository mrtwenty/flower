<?php

declare(strict_types=1);

namespace app\library;

use Flower\Client;

/**
 * 消息队列
 */
class Flower
{
    const VERSION = '0.0.2';

    protected $redis;
    protected $redisVersion;
    protected $client;
    protected $mq;

    public function __construct($redis, $mq, Client $client = null)
    {
        $this->redis = $redis;
        $this->client = $client;
        $this->mq = $mq;

        //获取到redis server的版本
        $server = $redis->info('server');
        $this->redisVersion = $server['redis_version'];
    }

    /**
     * 启动
     */
    public function start($consumer, BaseInterface $run, Status $status)
    {
        $mq_name    = $this->mq['name']; //消息队列名
        $group_name = $this->mq['group_name']; //分组名

        //先创建，这是为了避免还未创建stream，就使用xgroup
        $this->client->add('init');
        // $ 表示读取最新的 ， 0 表示从最小开始读取
        $this->redis->xGroup('CREATE', $mq_name, $group_name, '0');

        //循环读取消息:
        while (true) {

            //判断是否需要停止
            if ('stop' === $status->status()) {
                break;
            }

            //阻塞读取，每次读一个，超时5秒
            $res = $this->redis->xReadGroup($group_name, $consumer, [$mq_name => '>'], 1, 5000);

            //读取不到，就重新读取
            if (empty($res)) {
                continue;
            }

            //将读取的消息处理业务逻辑后，ack确认消息
            foreach ($res as $mq_data) {
                foreach ($mq_data as $msg_id => $msg_content) {
                    $this->consumer($group_name, $msg_id, $msg_content, $run);
                }
            }

            //概率修剪mq队列长度,近似修剪，避免长度太长，内存撑爆
            $this->trim();
        }
    }

    /**
     * 重试消费失败的消息，超出失败次数确认消息，写入队列
     *
     * @param mixed $run
     * @return void
     */
    public function pending(BaseInterface $run)
    {
        $mq_name         = $this->mq['name']; //消息队列名
        $group_name      = $this->mq['group_name']; //分组名
        $try_fail_num    = $this->mq['try_fail_num']; //尝试次数
        $try_fail_second = $this->mq['try_fail_second'] * 1000; //延迟重试间隔秒数
        $fail_list       = $this->mq['fail_list']; //失败列表名

        //读取未确认的消息
        if (version_compare($this->redisVersion, '6.2.0', '<')) {
            $pending = $this->redis->xPending($mq_name, $group_name, '-', '+', 100);
        } else {
            $pending = $this->redis->rawCommand('xpending', $mq_name, $group_name, 'idle', $try_fail_second, '-', '+', 100);
        }
        if (empty($pending)) {
            return;
        }

        foreach ($pending as $msg) {

            [$msg_id, $consumer, $over_time, $fail_num] = $msg;
            //未超过，不处理
            if ($over_time < $try_fail_second) {
                continue;
            }
            //消息内容
            $msg_data = $this->redis->xRange($mq_name, $msg_id, $msg_id);

            //如果找不到该消息
            if (empty($msg_data)) {
                continue;
            }

            //查询消息内容
            $msg_content = $msg_data[$msg_id];

            //如果该消息重试次数大于3,保存到失败队列
            if ($fail_num > $try_fail_num) {
                //消息内容
                $data = json_decode($msg_content['data'], true);
                $this->redis->lPush($fail_list, json_encode(['time' => time(), 'data' => $data], JSON_UNESCAPED_UNICODE));
                $this->client->ack($group_name, $msg_id);
                $run->fail($data, $msg_id);
                continue;
            }

            //消费内容
            if (!$this->consumer($group_name, $msg_id, $msg_content, $run)) {
                //如果处理失败，转移消息
                $this->redis->xClaim($mq_name, $group_name, $consumer, $over_time, [$msg_id]);
            }
        }
    }

    /**
     * 处理延迟队列的
     * @return [type] [description]
     */
    public function delay()
    {
        $mq_delay_name = $this->mq['delay_name'];

        $now  = (string) time();
        $list = $this->redis->zRevRangeByScore($mq_delay_name, $now, '-inf', ['limit' => [0, 128]]);
        if (empty($list)) {
            return;
        }

        foreach ($list as $package_str) {
            $result = $this->redis->zRem($mq_delay_name, $package_str);
            if ($result !== 1) {
                continue;
            }
            $package_arr = \json_decode($package_str, true);
            $this->client->add($package_arr['data']);
        }
    }

    /**
     * 消费数据
     *
     * @param mixed $msg_id
     * @param mixed $package_arr
     * @param mixed $run
     * @return void
     */
    protected function consumer($group_name, $msg_id, $package_arr, BaseInterface $run)
    {
        if (!isset($package_arr['data'])) {
            $this->client->ack($group_name, $msg_id);
            return true;
        }

        $package = \json_decode($package_arr['data'], true);

        if (!$package) {
            $this->client->ack($group_name, $msg_id);
            return true;
        }

        //如果是初始化消息或消息为空，就直接确认
        if ($package === 'init') {
            $this->client->ack($group_name, $msg_id);
            return true;
        }

        //执行业务逻辑
        if ($run->consumer($package, $msg_id)) {
            $this->client->ack($group_name, $msg_id);
            return true;
        }

        return false;
    }

    /**
     * 查看消息队列的运行情况
     *
     * @return array
     */
    public function info()
    {
        //状态
        $status     = new Status($this->mq, $this->redisConfig);
        $status_val = $status->status();


        $mq_name    = $this->mq['name']; //消息队列名
        $fail_list  = $this->mq['fail_list'];
        $delay_name = $this->mq['delay_name'];

        //stream
        $mq_info        = $this->redis->xinfo('stream', $mq_name);
        $group_info     = $this->redis->xinfo('groups', $mq_name);
        $consumers_info = [];
        foreach ($group_info as $group) {
            $consumers_info[$group['name']] = $this->redis->xinfo('consumers', $mq_name, $group['name']);
        }


        //延迟队列:
        $mq_delay_10 = [];
        $mq_delay_res = $this->redis->zRange($delay_name, 0, 10, true);
        foreach ($mq_delay_res as $key => $value) {
            $temp = json_decode($key, true);

            $mq_delay_10[] = [
                'origin' => [$key => $value],
                'time'   => date('Y-m-d H:i:s', (int)$value),
                'data'   => json_encode($temp['data']),
            ];
        }

        //失败队列:        
        $fail_list_10 = [];
        $fail_list_res = $this->redis->lRange($fail_list, 0, 10);
        foreach ($fail_list_res as $value) {
            $temp = json_decode($value, true);
            $fail_list_10[] = [
                'origin' => $value,
                'time' => date('Y-m-d H:i:s', $temp['time']),
                'data' => json_encode($temp['data']),
            ];
        }

        return [
            'mq_name'        => $mq_name,
            'mq_status'      => $status_val,
            'mq_info'        => $mq_info,                         //消息队列基本信息
            'mq_len'         => $this->redis->xlen($mq_name),     //
            'group_info'     => $group_info,                      //分组信息
            'consumers_info' => $consumers_info,                  //消费者信息
            'mq_delay_len'   => $this->redis->zCard($delay_name), //延迟队列存放的数据长度
            'mq_delay_10'    => $mq_delay_10,                     //最先执行的10个数据
            'fail_list_len'  => $this->redis->lLen($fail_list),   //失败存放的列表长度
            'fail_list_10'   => $fail_list_10,                    //最新的10条记录
        ];
    }

    /**
     * 修剪mq队列长度,近似修剪，避免长度太长，内存撑爆，概率触发
     * @return void
     */
    public function trim()
    {
        $mode = $this->mq['gc_mode'];

        if ('no' === $mode) {
            return false;
        }

        if (!$this->isGc()) {
            return false;
        }

        if ('maxlen' === $mode) {
            return $this->redis->xtrim($this->mq['name'], $this->mq['maxlen'], true);
        }

        if ('minid' === $mode) {
            $minid = new Minid($this->redis, $this->redisVersion, $this->mq['name']);
            return $minid->gc();
        }

        return false;
    }

    /**
     * 使用概率触发，模板php的session回收机制
     *
     * @return bool
     */
    protected function isGc()
    {
        $gc_probability = intval($this->mq['gc_probability']); //
        $gc_divisor = intval($this->mq['gc_divisor']); //
        $nrand = intval($gc_divisor * lcg_value());
        if ($gc_probability > 0 && $nrand < $gc_probability) {
            return true;
        }
        return false;
    }
}
