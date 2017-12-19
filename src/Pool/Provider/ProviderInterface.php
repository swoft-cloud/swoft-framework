<?php

namespace Swoft\Pool\Provider;

/**
 * the interface of provier
 *
 * @uses      ProviderInterface
 * @version   2017年07月23日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 Swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
interface ProviderInterface
{
    /**
     * @param string $serviceName
     *
     * @return mixed
     */
    public function getServiceList(string $serviceName, ...$params);

    /**
     * @param string $serviceName
     * @param string $host
     * @param int    $port
     *
     * @return mixed
     */
    public function registerService(string $serviceName, string $host, int $port, ...$params);
}
