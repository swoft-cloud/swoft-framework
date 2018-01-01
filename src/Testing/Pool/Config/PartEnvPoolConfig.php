<?php

namespace Swoft\Testing\Pool\Config;

use Swoft\Bean\Annotation\Bean;
use Swoft\Bean\Annotation\Value;
use Swoft\Pool\PoolProperties;
use Swoft\Pool\ProviderSelector;

/**
 * part env of config
 *
 * @Bean()
 * @uses      PartEnvPoolConfig
 * @version   2017年12月17日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class PartEnvPoolConfig extends PoolProperties
{
    /**
     * the name of pool
     *
     * @Value(env="${TEST_NAME}")
     * @var string
     */
    protected $name = "";

    /**
     * the maximum number of active connections
     *
     * @Value(env="${TEST_MAX_ACTIVE}")
     * @var int
     */
    protected $maxActive = 50;

    /**
     * the time of connect timeout
     *
     * @Value(env="${TEST_TIMEOUT}")
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
     * @Value(env="${TEST_URI}")
     * @var array
     */
    protected $uri = [];

    /**
     * the default provider is consul provider
     *
     * @Value(env="${TEST_PROVIDER}")
     * @var string
     */
    protected $provider = ProviderSelector::TYPE_CONSUL;
}