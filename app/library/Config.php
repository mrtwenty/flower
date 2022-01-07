<?php

declare(strict_types=1);

namespace app\library;

/**
 * 配置管理类
 */
class Config
{
    /**
     * 配置参数
     * @var array
     */
    protected $config = [];


    /**
     * 加载配置文件（多种格式）
     * @access public
     * @param  string $file 配置文件名
     * @return array
     */
    public function load(string $file): array
    {
        $this->config = include $file;
        return $this->config;
    }

    /**
     * 检测配置是否存在
     * @access public
     * @param  string $name 配置参数名（支持多级配置 .号分割）
     * @return bool
     */
    public function has(string $name): bool
    {
        if (false === strpos($name, '.') && !isset($this->config[strtolower($name)])) {
            return false;
        }

        return !is_null($this->get($name));
    }

    /**
     * 获取一级配置
     * @access protected
     * @param  string $name 一级配置名
     * @return array
     */
    protected function pull(string $name): array
    {
        $name = strtolower($name);

        return $this->config[$name] ?? [];
    }

    /**
     * 获取配置参数 为空则获取所有配置
     * @access public
     * @param  string $name    配置参数名（支持多级配置 .号分割）
     * @param  mixed  $default 默认值
     * @return mixed
     */
    public function get(string $name = null, $default = null)
    {
        // 无参数时获取所有
        if (empty($name)) {
            return $this->config;
        }

        if (false === strpos($name, '.')) {
            return $this->pull($name);
        }

        $name    = explode('.', $name);
        $name[0] = strtolower($name[0]);
        $config  = $this->config;

        // 按.拆分成多维数组进行判断
        foreach ($name as $val) {
            if (isset($config[$val])) {
                $config = $config[$val];
            } else {
                return $default;
            }
        }

        return $config;
    }
}
