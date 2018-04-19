<?php

namespace Swoft\Sg\Balancer;

use Swoft\Bean\Annotation\Bean;

/**
 * 轮询负载
 *
 * @Bean()
 */
class RoundRobinBalancer implements BalancerInterface
{
    private $lastIndex = 0;

    public function select(array $serviceList, ...$params)
    {
        $currentIndex = $this->lastIndex + 1;
        if ($currentIndex+1 > count($serviceList)) {
            $currentIndex = 0;
        }

        $this->lastIndex = $currentIndex;
        return $serviceList[$currentIndex];
    }
}
