<?php

namespace Swoft\Pool;

use Swoft\App;
use Swoft\Exception\InvalidArgumentException;
use Swoft\Pool\Provider\ConsulProvider;
use Swoft\Pool\Provider\ProviderInterface;

/**
 * the selector of service provider
 *
 * @uses      ProviderSelector
 * @version   2017年12月16日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class ProviderSelector implements SelectorInterface
{
    /**
     * consul
     */
    const TYPE_CONSUL = 'consul';

    /**
     * @var array
     */
    private $providers
        = [

        ];

    /**
     * get provider from selector
     *
     * @param string $type
     *
     * @return ProviderInterface
     */
    public function select(string $type)
    {
        $providers = $this->mergeProviders();
        if (!isset($providers[$type])) {
            throw new InvalidArgumentException("the provider {$type} is not exist!");
        }

        $providerBeanName = $providers[$type];

        return App::getBean($providerBeanName);
    }

    /**
     * merge default and config packers
     *
     * @return array
     */
    private function mergeProviders()
    {
        return array_merge($this->providers, $this->defaultProvivers());
    }

    /**
     * the balancers of default
     *
     * @return array
     */
    private function defaultProvivers()
    {
        return [
            self::TYPE_CONSUL => ConsulProvider::class,
        ];
    }
}