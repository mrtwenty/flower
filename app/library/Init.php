<?php

declare(strict_types=1);

namespace app\library;

use app\library\Flower;
use Flower\Client;
use Workerman\Timer;
use Workerman\Worker;

/**
 * 初始化类
 */
class Init
{
    protected $redisConfig;
    protected $run;
    protected $mq;
    protected $isLinux;

    public function __construct(BaseInterface $run, $redis_config, $mq)
    {
        global $argc, $argv;

        $this->run         = $run;
        $this->redisConfig = $redis_config;
        $this->mq          = $mq;

        $temp = $mq['name'];

        //指定存放日志和pid的地方
        $runtime         = dirname(dirname(__DIR__)) . '/runtime';
        Worker::$logFile = sprintf('%s/%s', $runtime, $temp . '.log');
        Worker::$pidFile = sprintf('%s/%s', $runtime, $temp . '.pid');

        //check
        if ($argc == 1) {
            exit('missing parameter');
        }

        //根据参数执行
        $method = $argv[1];
        $param  = ['start', 'pending', 'delay', 'stop', 'config', 'test', 'status'];

        if (!in_array($method, $param, true)) {
            exit('parameter error');
        }

        $this->isLinux = is_linux();
        $this->$method();
    }

    /**
     * 消费者
     */
    public function start()
    {
        $run          = $this->run;
        $redis_config = $this->redisConfig;
        $mq           = $this->mq;
        $worker       = new Worker();
        $worker->name = 'consumer';

        // 启动n个进程对外提供服务
        $worker->count = $mq['consumer_num'];

        $worker->onWorkerStart = function ($worker) use ($run, $redis_config, $mq) {

            $redis    = redis($redis_config);
            $client   = new Client($redis, $mq);
            $flower   = new Flower($redis, $mq, $client);
            $consumer = 'consumer-' . $worker->id; //消费者
            $flower->start($consumer, $run);
        };

        if ($this->isLinux) {
            $this->pending();
            $this->delay();
        } else {
            //7.4
            if (function_exists('sapi_windows_set_ctrl_handler')) {
                sapi_windows_set_ctrl_handler(function () {
                    //设置为stop
                    $redis  = redis($this->redisConfig);
                    $flower = new Flower($redis, $this->mq);
                    $flower->stop();
                    exit;
                });
            }
        }

        // 运行worker
        Worker::runAll();
    }

    /**
     * 失败重试
     */
    public function pending()
    {
        $redis_config = $this->redisConfig;
        $mq           = $this->mq;
        $run          = $this->run;

        $worker       = new Worker();
        $worker->name = 'pending';

        $worker->onWorkerStart = function ($worker) use ($redis_config, $run, $mq) {

            $redis  = redis($redis_config);
            $client = new Client($redis, $mq);
            $flower = new Flower($redis, $mq, $client);

            $time_interval = 0.5;
            Timer::add($time_interval, function () use ($flower, $run) {
                $flower->pending($run);
            });
        };

        // 运行worker
        if (!$this->isLinux) {
            Worker::runAll();
        }
    }

    /**
     * 延迟队列
     */
    public function delay()
    {
        $redis_config = $this->redisConfig;
        $mq           = $this->mq;
        $worker       = new Worker();
        $worker->name = 'delay';

        $worker->onWorkerStart = function ($worker) use ($redis_config, $mq) {

            $redis  = redis($redis_config);
            $client = new Client($redis, $mq);
            $flower = new Flower($redis, $mq, $client);

            $time_interval = 1;
            Timer::add($time_interval, function () use ($flower) {
                $flower->delay();
            });
        };

        // 运行worker
        if (!$this->isLinux) {
            Worker::runAll();
        }
    }

    /**
     * 查看Flower当前运行状态
     */
    public function status()
    {
        // 运行worker
        Worker::runAll();
    }

    /**
     * 停止Flower的运行
     */
    public function stop()
    {
        $redis  = redis($this->redisConfig);
        $flower = new Flower($redis, $this->mq);
        $flower->stop();
        // 运行worker
        Worker::runAll();
    }

    /**
     * 查看当前配置信息
     */
    public function config()
    {
        print_r($this->config);
    }

    public function test()
    {

        $redis  = redis($this->redisConfig);
        $client = new Client($redis, $this->mq);

        $data   = [
            'name' => 'mrtwenty',
            'age'  => mt_rand(0, 100),
            'desc' => 'a queue msg',
        ];
        $id = $client->add($data);

        echo "queue:";
        print_r($id);

        echo "\n===============\n";
        $data = [
            'name' => 'mrtwenty',
            'age'  => mt_rand(0, 100),
            'desc' => 'a delay queue msg',
        ];

        $delay = 10;
        $id    = $client->add($data, $delay);
        echo "delay queue:";
        print_r($id);
        echo "\n";
    }
}
