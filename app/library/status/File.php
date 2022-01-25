<?php

declare(strict_types=1);

namespace app\library\status;

/**
 * 
 */
class File implements StatusInterface
{
    protected $file;

    public function __construct($key)
    {
        $file = dirname(dirname(dirname(__DIR__))) . '/runtime/' . $key;
        $this->file   = $file;
    }

    public function start()
    {
        if (file_put_contents($this->file, "start")) {
            return true;
        }
        throw new \Exception("file write error", 1);
    }

    public function stop()
    {
        if (file_put_contents($this->file, "stop")) {
            return true;
        }
        throw new \Exception("file write error", 1);
    }

    public function status(): string
    {
        if (file_exists($this->file)) {
            return file_get_contents($this->file);
        }
        return 'stop';
    }
}
