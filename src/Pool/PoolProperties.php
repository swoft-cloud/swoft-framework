<?php

namespace Swoft\Pool;
use Swoft\Sg\BalancerSelector;
use Swoft\Sg\ProviderSelector;

/**
 * Pool properties
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
     * Minimum active number of connections
     *
     * @var int
     */
    protected $minActive = 5;

    /**
     * Maximum active number of connections
     *
     * @var int
     */
    protected $maxActive = 10;

    /**
     * Maximum waiting for the number of connections, if there is no limit to 0
     *
     * @var int
     */
    protected $maxWait = 20;

    /**
     * Maximum waiting time
     *
     * @var int
     */
    protected $maxWaitTime = 3;

    /**
     * Maximum idle time
     *
     * @var int
     */
    protected $maxIdleTime = 60;

    /**
     * the time of connect timeout
     *
     * @var int
     */
    protected $timeout = 3;

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
     * Initialize
     */
    public function init()
    {
        if (empty($this->name)) {
            $this->name = uniqid();
        }
    }

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
     * @return int
     */
    public function getMinActive(): int
    {
        return $this->minActive;
    }

    /**
     * @return int
     */
    public function getMaxWaitTime(): int
    {
        return $this->maxWaitTime;
    }

    /**
     * @return int
     */
    public function getMaxIdleTime(): int
    {
        return $this->maxIdleTime;
    }
}
