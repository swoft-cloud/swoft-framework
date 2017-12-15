<?php

namespace Swoft\Service;

/**
 *
 *
 * @uses      ProviderInterface
 * @version   2017年07月23日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 Swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
interface ProviderInterface
{
    public function getServiceList(string $serviceName);
    public function registerService(string $serviceName, $host, $port, $tags = [], $interval = 10, $timeout = 1);
}
