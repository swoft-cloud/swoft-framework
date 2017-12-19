<?php

namespace Swoft\Pool;

/**
 * the properties of pool
 *
 * @uses      PoolProperties
 * @version   2017年12月16日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class PoolProperties implements PoolConfigInterface
{
    /**
     * the name of pool
     *
     * @var string
     */
    protected $name = "";

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
     * @var string
     */
    protected $balancer = BalancerSelector::TYPE_RANDOM;

    /**
     * the default provider is consul provider
     *
     * @var string
     */
    protected $provider = ProviderSelector::TYPE_CONSUL;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getMaxIdel(): int
    {
        return $this->maxIdel;
    }

    /**
     * @return int
     */
    public function getMaxActive(): int
    {
        return $this->maxActive;
    }

    /**
     * @return int
     */
    public function getMaxWait(): int
    {
        return $this->maxWait;
    }

    /**
     * @return int
     */
    public function getTimeout(): int
    {
        return $this->timeout;
    }

    /**
     * @return array
     */
    public function getUri(): array
    {
        return $this->uri;
    }

    /**
     * @return bool
     */
    public function isUseProvider(): bool
    {
        return $this->useProvider;
    }

    /**
     * @return string
     */
    public function getBalancer(): string
    {
        return $this->balancer;
    }

    /**
     * @return string
     */
    public function getProvider(): string
    {
        return $this->provider;
    }

    /**
     * init
     */
    public function init()
    {
        if(empty($this->name)){
            $this->name = uniqid();
        }
    }
}