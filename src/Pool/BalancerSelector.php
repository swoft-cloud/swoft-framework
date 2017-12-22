<?php

namespace Swoft\Pool;

use Swoft\App;
use Swoft\Pool\Balancer\RandomBalancer;
use Swoft\Pool\Balancer\RoundRobinBalancer;
use Swoft\Pool\Balancer\BalancerInterface;

/**
 * the manager of balancer
 *
 * @uses      BalancerSelector
 * @version   2017年12月16日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class BalancerSelector implements SelectorInterface
{
    /**
     * the name of random
     */
    const TYPE_RANDOM = 'random';

    /**
     * the name of roundRobin
     */
    const TYPE_ROUND_ROBIN = 'roundRobin';

    /**
     * @var array
     */
    private $balancers = [

    ];

    /**
     * get balancer
     *
     * @param string $type
     *
     * @return BalancerInterface
     */
    public function select(string $type)
    {
        $balancers = $this->mergeBalancers();
        if(!isset($balancers[$type])){

        }

        $balancerBeanName = $balancers[$type];
        return App::getBean($balancerBeanName);
    }

    /**
     * merge default and config packers
     *
     * @return array
     */
    private function mergeBalancers()
    {
        return array_merge($this->balancers, $this->defaultBalancers());
    }

    /**
     * the balancers of default
     *
     * @return array
     */
    private function defaultBalancers()
    {
        return [
            self::TYPE_RANDOM => RandomBalancer::class,
            self::TYPE_ROUND_ROBIN => RoundRobinBalancer::class
        ];
    }
}