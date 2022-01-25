<?php

/**
 * 助手函数
 */

use app\library\Env;
use app\library\Config;

/**
 * 获取环境变量的配置
 *
 * @param string|null $key 
 * @param mixed $default 
 * @return mixed
 */
function env(string $key = null, $default = null)
{
    static $static_env = null;
    if (!$static_env) {
        //初始化配置
        $env = new Env();
        $env_file = dirname(__DIR__) . '/.env';
        if (file_exists($env_file)) {
            $env->load($env_file);
        }
        $static_env = $env;
    }
    return $static_env->get($key, $default);
}

/**
 * 获取系统的配置，如果没有环境配置，会按照默认配置执行
 *
 * @param string|null $key 
 * @param mixed $default 
 * @return mixed
 */
function config(string $key = null, $default = null)
{
    static $static_config = null;
    if (!$static_config) {
        $config = new Config();
        $config->load(__DIR__ . '/config/app.php');

        $static_config = $config;
    }
    return $static_config->get($key, $default);
}

/**
 * redis实例
 *
 * @param array $config 
 * @param string $key     master、slave 
 */
function redis($config, $key = 'slave')
{
    static $static_redis = [];

    if (isset($static_redis[$key])) {
        return $static_redis[$key];
    }

    //判断某个扩展名是否在其中
    if (!extension_loaded('redis')) {
        throw new \Exception("redis extension no found", 1);
    }

    $redis_host = $config['host'];
    $redis_port = $config['port'];
    $redis_auth = $config['auth'];
    $redis_db   = $config['database'];

    $redis = new \Redis();
    if ($redis->connect($redis_host, $redis_port) !== true) {
        throw new \Exception("redis connect error", 1);
    }

    //密码
    if ($redis_auth !== '' && $redis->auth($redis_auth) !== true) {
        throw new \Exception("redis auth error", 1);
    }

    //测试链接
    if (!$redis->ping()) {
        throw new \Exception("redis connect error", 1);
    }

    //选择数据库
    $redis->select($redis_db);
    $static_redis[$key] = $redis;
    return $redis;
}

/**
 * 判断是否是linux
 *
 * @return bool
 */
function is_linux()
{
    if (\DIRECTORY_SEPARATOR === '\\') {
        return false;
    }
    return true;
}
