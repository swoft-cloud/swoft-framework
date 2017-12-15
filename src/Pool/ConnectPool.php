<?php

namespace Swoft\Pool;

use Swoft\App;
use Swoft\Bean\Annotation\Inject;
use Swoft\Pool\Balancer\IBalancer;
use Swoft\Service\ProviderInterface;

/**
 * 通用连接池
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
     * @var string 服务名称
     */
    protected $serviceName = "";

    /**
     * the maximum number of idle connections
     *
     * @var int
     */
    protected $maxIdel = 6;

    /**
     * the maximum number of active connections
     *
     * @var int
     */
    protected $maxActive = 50;

    /**
     * the maximum number of wait connections
     *
     * @var int
     */
    protected $maxWait = 100;

    /**
     * the time of connect timeout
     *
     * @var int
     */
    protected $timeout = 200;

    /**
     * the addresses of connection
     *
     * <pre>
     * [
     *  '127.0.0.1:88',
     *  '127.0.0.1:88'
     * ]
     * </pre>
     *
     * @var array
     */
    protected $uri = [];

    /**
     * whether to user provider(consul/etcd/zookeeper)
     *
     * @var bool
     */
    protected $useProvider = false;

    /**
     * the default balancer is random balancer
     *
     * @Inject()
     * @var IBalancer;
     */
    protected $balancer = null;

    /**
     * the default provider is consul provider
     *
     * @Inject()
     * @var ProviderInterface
     */
    protected $serviceProvider = null;

    /**
     * @var int 当前连接数
     */
    protected $currentCounter = 0;

    /**
     * @var \SplQueue 连接队列
     */
    protected $queue = null;


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
        if ($this->currentCounter > $this->maxActive) {
            return null;
        }
        if (!$this->queue->isEmpty()) {
            $connect = $this->queue->shift();
            return $connect;
        }

        $connect = $this->createConnect();
        if ($connect !== null) {
            $this->currentCounter++;
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
        if ($this->queue->count() < $this->maxActive) {
            $this->queue->push($connect);
            $this->currentCounter--;
        }
    }

    /**
     * 获取一个连接串
     *
     * @return string 如:"127.0.0.1:88"
     */
    public function getConnectAddress()
    {
        $serviceList = $this->getServiceList();
        return $this->balancer->select($serviceList);
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
        if ($this->useProvider) {
            return $this->serviceProvider->getServiceList($this->serviceName);
        }

        if (empty($this->uri)) {
            App::error($this->serviceName."服务，没有配置uri");
            throw new \InvalidArgumentException($this->serviceName."服务，没有配置uri");
        }
        return $this->uri;
    }

    /**
     * @return int
     */
    public function getTimeout(): int
    {
        return $this->timeout;
    }

    abstract public function createConnect();

    abstract public function reConnect($client);
}
