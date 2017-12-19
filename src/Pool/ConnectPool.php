<?php

namespace Swoft\Pool;

use Swoft\App;

/**
 * the pool of connection
 *
 * @uses      ConnectPool
 * @version   2017年06月15日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 Swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
abstract class ConnectPool implements IPool
{
    /**
     * the nubmer of current connections
     *
     * @var int
     */
    protected $currentCount = 0;

    /**
     * the queque of connection
     *
     * @var \SplQueue
     */
    protected $queue = null;

    /**
     * the config of pool
     *
     * @var PoolConfigInterface
     */
    protected $poolConfig;

    /**
     * 连接池中取一个连接
     *
     * @return object|null
     */
    public function getConnect()
    {
        if ($this->queue == null) {
            $this->queue = new \SplQueue();
        }

        $connect = null;
        if ($this->currentCount > $this->poolConfig->getMaxActive()) {
            return null;
        }
        if (!$this->queue->isEmpty()) {
            $connect = $this->queue->shift();

            return $connect;
        }

        $connect = $this->createConnect();
        if ($connect !== null) {
            $this->currentCount++;
        }

        return $connect;
    }

    /**
     * 释放一个连接到连接池
     *
     * @param object $connect 连接
     */
    public function release($connect)
    {
        if ($this->queue->count() < $this->poolConfig->getMaxActive()) {
            $this->queue->push($connect);
            $this->currentCount--;
        }
    }

    /**
     * 获取一个连接串
     *
     * @return string 如:"127.0.0.1:88"
     */
    public function getConnectAddress()
    {
        $serviceList  = $this->getServiceList();
        $balancerType = $this->poolConfig->getBalancer();
        $balancer     = App::getBalancerSelector()->select($balancerType);

        return $balancer->select($serviceList);
    }

    /**
     * 获取一个可以用服务列表
     *
     * @return array
     * <pre>
     * [
     *   "127.0.0.1:88",
     *   "127.0.0.1:88"
     * ]
     * </pre>
     */
    protected function getServiceList()
    {
        $providerSelector = App::getProviderSelector();
        $name             = $this->poolConfig->getName();
        if ($this->poolConfig->isUseProvider()) {
            $type = $this->poolConfig->getProvider();

            return $providerSelector->select($type)->getServiceList($name);
        }

        $uri = $this->poolConfig->getUri();
        if (empty($uri)) {
            App::error("$name 服务，没有配置uri");
            throw new \InvalidArgumentException("$name 服务，没有配置uri");
        }

        return $uri;
    }

    /**
     * @return int
     */
    public function getTimeout(): int
    {
        return $this->poolConfig->getTimeout();
    }

    abstract public function createConnect();

    abstract public function reConnect($client);
}
