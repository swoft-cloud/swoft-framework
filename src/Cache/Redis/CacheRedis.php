<?php

namespace Swoft\Cache\Redis;

use Swoft\App;
use Swoft\Cache\CacheInterface;
use Swoft\Cache\CacheResult;
use Swoft\Pool\RedisPool;

/**
 * the cache of redis
 *
 * @uses      CacheRedis
 * @version   2017年12月24日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class CacheRedis implements CacheInterface
{
    public function get($key, $default = null)
    {
        $result = $this->call('get', [$key]);
        if ($result === false) {
            return $default;
        }

        return $result;
    }

    public function set($key, $value, $ttl = null)
    {
        $ttl = $this->getTtl($ttl);

        return $this->call('set', [$key, $value, $ttl]);
    }

    public function delete($key)
    {
        return $this->call('delete', [$key]);
    }

    public function clear()
    {

    }

    public function getMultiple($keys, $default = null)
    {
        $result = $this->call('mget', [$keys]);
        if($result === false){
            return $default;
        }
        return $result;
    }

    public function setMultiple($values, $ttl = null)
    {
        $ttl = $this->getTtl($ttl);
        return $this->call('mset', [$values]);
    }

    public function deleteMultiple($keys)
    {

    }

    public function has($key)
    {

    }

    private function getTtl($ttl)
    {
        return ($ttl == null) ? 0 : (int)$ttl;
    }

    public function deferCall(string $method, array $params)
    {
        $connectPool = App::getPool(RedisPool::class);

        /* @var $client RedisConnect */
        $client = $connectPool->getConnect();
        $client->setDefer();
        $result = $client->$method(...$params);

        return new CacheResult($connectPool, $client, "", $result);
    }

    public function __call($method, $arguments)
    {
        return self::call($method, $arguments);
    }

    private function call(string $method, array $params)
    {
        /** @var \Swoft\Pool\ConnectPool $connectPool */
        $connectPool = App::getBean(RedisPool::class);

        /* @var RedisConnect $client */
        $client = $connectPool->getConnect();
        $result = $client->$method(...$params);
        $connectPool->release($client);

        return $result;
    }

}