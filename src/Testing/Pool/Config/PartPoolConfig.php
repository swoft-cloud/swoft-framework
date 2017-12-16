<?php

namespace Swoft\Testing\Pool\Config;

use Swoft\Bean\Annotation\Bean;
use Swoft\Bean\Annotation\Value;
use Swoft\Pool\BalancerSelector;
use Swoft\Pool\PoolProperties;

/**
 * the part properties of default
 *
 * @Bean()
 * @uses      PartPoolConfig
 * @version   2017年12月16日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class PartPoolConfig extends PoolProperties
{
    /**
     * the name of pool
     *
     * @Value(name="${config.test.test.name}")
     * @var string
     */
    protected $name = "";

    /**
     * the maximum number of idle connections
     *
     * @Value(name="${config.test.test.maxIdel}")
     * @var int
     */
    protected $maxIdel = 6;

    /**
     * the maximum number of active connections
     *
     * @Value(name="${config.test.test.maxActive}")
     * @var int
     */
    protected $maxActive = 50;

    /**
     * the maximum number of wait connections
     *
     * @Value(name="${config.test.test.maxWait}")
     * @var int
     */
    protected $maxWait = 100;


    /**
     * the default balancer is random balancer
     *
     * @Value(name="${config.test.test.balancer}")
     * @var string
     */
    protected $balancer = BalancerSelector::TYPE_RANDOM;
}