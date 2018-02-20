<?php

namespace Swoft\Pool\Provider;

/**
 * Provier interface
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
     *
     * @return mixed
     */
    public function registerService(string $serviceName , ...$params);
}
