<?php

namespace Swoft\Pool;

use Swoft\App;
use Swoft\Bean\Annotation\Bean;
use Swoft\Bean\Annotation\Inject;
use Swoft\Bean\Annotation\Pool;
use Swoft\Cache\Redis\RedisConnect;
use Swoft\Cache\Redis\SyncRedisConnect;
use Swoft\Pool\Config\RedisPoolConfig;

/**
 * redis连接池
 *
 * @Pool()
 * @uses      RedisPool
 * @version   2017年05月11日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 Swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class RedisPool extends ConnectPool
{
    /**
     * the config of redis pool
     *
     * @Inject()
     * @var RedisPoolConfig
     */
    protected $poolConfig;

    /**
     * 创建一个连接
     *
     * @return RedisConnect|SyncRedisConnect
     */
    public function createConnect()
    {
        if (App::isWorkerStatus()) {
            return new RedisConnect($this);
        }
        return new SyncRedisConnect($this);
    }

    public function reConnect($client)
    {
        list($host, $port) = $this->getConnectInfo();
    }
}
