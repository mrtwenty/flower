<?php

declare(strict_types=1);

namespace app\library;

use app\controller\Action;
use Workerman\Connection\TcpConnection;
use Workerman\Protocols\Http\Request;
use Workerman\Protocols\Http\Response;
use Workerman\Worker;

/**
 * 监控
 */
class Monitor
{
    protected $name = 'monitor';

    public function __construct()
    {
        //指定存放日志和pid的地方
        $runtime         = dirname(dirname(__DIR__)) . '/runtime';
        Worker::$logFile = sprintf('%s/%s', $runtime, $this->name . '.log');
        Worker::$pidFile = sprintf('%s/%s', $runtime, $this->name . '.pid');

        $this->monitor();
    }

    /**
     * 监控
     */
    public function monitor()
    {
        $worker = new Worker(config('monitor.socket'));
        $worker->name = $this->name;

        $worker->onWorkerStart = function () {
        };

        $worker->onMessage = function (TcpConnection $connection, Request $request) {

            $method = ltrim($request->path(), '/');
            $method = $method === '' ? 'index' : $method;
            //动态
            $action = new Action($request);
            if (is_callable([$action, $method])) {
                $str = $action->$method();
                $connection->send($str);
                return;
            }

            //静态
            $file = dirname(dirname(__DIR__)) . '/public/' . $method;
            if (file_exists($file)) {
                $response = (new Response())->withFile($file);
                $connection->send($response);
                return;
            }

            $connection->send('flower');
        };

        // 运行worker
        Worker::runAll();
    }
}
