

# 消费者中操作MySQL

可以安装此依赖包，当然也可以根据自己需要用别的包

```shell
composer require workerman/mysql
```

配置信息可以在.env里面写入: 

```shell
[mysql]
host = 127.0.0.1
username = root
password = 123456
database = test
port     = 3306
```

代码实现:

```php
<?php

declare(strict_types=1);

namespace app;

use app\library\BaseInterface;

/**
 * 消费类
 */
class Run implements BaseInterface
{
    protected $db = null;

    public function getDb()
    {
        if (is_null($this->db)) {
            $config = config('mysql');
            $host     = $config['host'];
            $port     = $config['port'];
            $user     = $config['username'];
            $password = $config['password'];
            $database  = $config['database'];
            $this->db = new \Workerman\MySQL\Connection($host, $port, $user, $password, $database);
        }
        return $this->db;
    }

    /**
     * 消费方法，如何消费，取决用户自己
     *
     * @param mixed $data
     * @param mixed $id
     * @return bool 返回true就会执行ack确认消息已消费
     */
    public function consumer($data, $id): bool
    {
        $db   = $this->getDb();
        $info = $db->row("SELECT * FROM `short_url` WHERE id=3");
        print_r($info);
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
```