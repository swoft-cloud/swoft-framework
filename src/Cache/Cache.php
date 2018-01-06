<?php

namespace Swoft\Cache;

use Swoft\App;
use Swoft\Cache\Redis\CacheRedis;

/**
 * cache
 *
 * @method string|bool get($key, $default = null)
 * @method bool set($key, $value, $ttl = null)
 * @method int delete($key)
 * @method bool clear()
 * @method array getMultiple($keys, $default = null)
 * @method bool setMultiple($values, $ttl = null)
 * @method int deleteMultiple($keys)
 * @method int has($key)
 *
 * @uses      Cache
 * @version   2018年01月04日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class Cache
{
    /**
     * @var string
     */
    private $driver = 'redis';

    /**
     * @var array
     */
    private $drivers = [];

    /**
     * get cache by driver
     *
     * @param string|null $driver
     *
     * @throws \InvalidArgumentException
     *
     * @return CacheInterface
     */
    public function getCache(string $driver = null): CacheInterface
    {
        $cacheDriver = $this->driver;
        $drivers     = $this->mergeDrivers();

        if ($driver != null) {
            $cacheDriver = $driver;
        }

        if (!isset($drivers[$cacheDriver])) {
            throw new \InvalidArgumentException("the driver of cache is not exist! driver=" . $cacheDriver);
        }

        return App::getBean($drivers[$cacheDriver]);
    }

    /**
     * magic method
     *
     * @param string $method
     * @param array  $arguments
     *
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        $cache = $this->getCache();

        return $cache->$method(...$arguments);
    }

    /**
     * merge driver
     *
     * @return array
     */
    private function mergeDrivers()
    {
        return array_merge($this->drivers, $this->defaultDrivers());
    }

    /**
     * the drivers of default
     *
     * @return array
     */
    private function defaultDrivers()
    {
        return [
            'redis' => CacheRedis::class,
        ];
    }
}
