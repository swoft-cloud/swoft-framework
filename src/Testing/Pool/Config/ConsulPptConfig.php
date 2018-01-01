<?php

namespace Swoft\Testing\Pool\Config;

use Swoft\Bean\Annotation\Bean;
use Swoft\Bean\Annotation\Value;
use Swoft\Pool\Provider\ProviderInterface;

/**
 * the properties of config
 *
 * @Bean()
 * @uses      ConsulPptConfig
 * @version   2017年12月19日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class ConsulPptConfig implements ProviderInterface
{
    /**
     * adress
     *
     * @Value(name="${config.provider.consul.address}")
     * @var string
     */
    private $address = 'http://127.0.0.1:80';

    /**
     * the tags of register service
     *
     * @Value(name="${config.provider.consul.tags}")
     * @var array
     */
    private $tags = [];

    /**
     * the timeout of consul
     *
     * @Value(name="${config.provider.consul.timeout}")
     * @var int
     */
    private $timeout = 300;

    /**
     * the interval of register service
     *
     * @Value(name="${config.provider.consul.interval}")
     * @var int
     */
    private $interval = 3;

    public function getServiceList(string $serviceName, ...$params)
    {
        // TODO: Implement getServiceList() method.
    }

    public function registerService(string $serviceName, string $host, int $port, ...$params)
    {
        // TODO: Implement registerService() method.
    }

    /**
     * @return string
     */
    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * @return array
     */
    public function getTags(): array
    {
        return $this->tags;
    }

    /**
     * @return int
     */
    public function getTimeout(): int
    {
        return $this->timeout;
    }

    /**
     * @return int
     */
    public function getInterval(): int
    {
        return $this->interval;
    }
}