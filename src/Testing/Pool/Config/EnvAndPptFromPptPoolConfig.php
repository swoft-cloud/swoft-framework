<?php

namespace Swoft\Testing\Pool\Config;

use Swoft\Bean\Annotation\Bean;
use Swoft\Bean\Annotation\Value;
use Swoft\Pool\BalancerSelector;
use Swoft\Pool\PoolProperties;
use Swoft\Pool\ProviderSelector;

/**
 * properties and env
 *
 * @Bean()
 * @uses      EnvAndPptPoolConfig
 * @version   2017年12月17日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class EnvAndPptFromPptPoolConfig extends PoolProperties
{
    /**
     * the name of pool
     *
     * @Value(name="${config.test.test2.name}", env="${TEST2_NAME}")
     * @var string
     */
    protected $name = "";

    /**
     * the maximum number of idle connections
     *
     * @Value(name="${config.test.test2.maxIdel}", env="${TEST2_MAX_IDEL}")
     * @var int
     */
    protected $maxIdel = 6;

    /**
     * the maximum number of active connections
     *
     * @Value(name="${config.test.test2.maxActive}", env="${TEST2_MAX_ACTIVE}")
     * @var int
     */
    protected $maxActive = 50;

    /**
     * the maximum number of wait connections
     *
     * @Value(name="${config.test.test2.maxWait}", env="${TEST2_MAX_WAIT}")
     * @var int
     */
    protected $maxWait = 100;

    /**
     * the time of connect timeout
     *
     * @Value(name="${config.test.test2.timeout}", env="${TEST2_TIMEOUT}")
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
     * @Value(name="${config.test.test2.uri}", env="${TEST2_URI}")
     * @var array
     */
    protected $uri = [];

    /**
     * whether to user provider(consul/etcd/zookeeper)
     *
     * @Value(name="${config.test.test2.useProvider}", env="${TEST2_USE_PROVIDER}")
     * @var bool
     */
    protected $useProvider = false;

    /**
     * the default balancer is random balancer
     *
     * @Value(name="${config.test.test2.balancer}", env="${TEST2_BALANCER}")
     * @var string
     */
    protected $balancer = BalancerSelector::TYPE_RANDOM;

    /**
     * the default provider is consul provider
     *
     * @Value(name="${config.test.test2.provider}", env="${TEST2_PROVIDER}")
     * @var string
     */
    protected $provider = ProviderSelector::TYPE_CONSUL;
}
