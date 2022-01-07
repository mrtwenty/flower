<?php

declare(strict_types=1);

namespace app\controller;

use app\library\Flower;
use Workerman\Protocols\Http\Request;
use Workerman\Protocols\Http\Response;
use Workerman\Worker;

/**
 * 监控
 */
class Action
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function login()
    {
        $data = [];
        if ('POST' === $this->request->method()) {
            $session = $this->request->session();
            $username = $this->request->post('username');
            $password = $this->request->post('password');
            if (config('monitor.username') === $username && config('monitor.password') === $password) {
                $session->set('is_login', 1);
                $session->set('username', $username);
                return $this->location('index');
            }
            $data['msg'] = '您输入的账号或密码有误!';
        }

        return $this->view('login', $data);
    }

    public function index()
    {
        if (!$this->auth()) {
            return $this->location('login');
        }

        $sys_info = [
            'flower'    => Flower::VERSION,
            'php'       => PHP_VERSION,
            'workerman' => Worker::VERSION,
        ];

        $redis = redis(config('redis'));
        $redis_info = $redis->info();

        return $this->view('index', [
            'sys_info' => $sys_info,
            'redis_info' => $redis_info,
        ]);
    }

    public function mq()
    {
        if (!$this->auth()) {
            return $this->location('login');
        }

        $redis = redis(config('redis'));
        $config = config('flower.mq');
        $flower = new Flower($redis, $config);
        $info = $flower->info();

        return $this->view('mq', ['config' => $config, 'info' => $info]);
    }

    public function logout()
    {
        $session = $this->request->session();
        $session->set('is_login', 0);
        return $this->location('login');
    }

    /**
     * 判断是否登录
     */
    protected function auth()
    {
        $session = $this->request->session();
        if ($session->get('is_login')) {
            return true;
        }
        return false;
    }

    /**
     * 跳转
     *
     * @param mixed $url 
     * @return Response
     */
    protected function location($url)
    {
        $url = '/' . $url;
        return new Response(302, ['Location' => $url]);
    }

    /**
     * 加载模板
     *
     * @param mixed $file 
     * @param array $vars 
     * @return string
     */
    protected function view($file, $vars = [])
    {
        $view_path = dirname(__DIR__) . '/view/' . $file . '.php';

        \extract($vars);
        \ob_start();
        // Try to include php file.
        try {
            include $view_path;
        } catch (\Throwable $e) {
            echo $e;
        }
        return \ob_get_clean();
    }
}
