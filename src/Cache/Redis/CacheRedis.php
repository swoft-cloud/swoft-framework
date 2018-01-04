<?php

namespace Swoft\Cache\Redis;

use Swoft\App;
use Swoft\Bean\Annotation\Bean;
use Swoft\Cache\CacheInterface;
use Swoft\Cache\CacheResult;
use Swoft\Pool\RedisPool;
use Swoole\Redis;

/**
 * the cache of redis
 *
 * key and string
 *
 * @method int append($key, $value)
 * @method int decr($key)
 * @method int decrBy($key, $value)
 * @method string getRange($key, $start, $end)
 * @method int incr($key)
 * @method int incrBy($key, $value)
 * @method float incrByFloat($key, $increment)
 * @method int strlen($key)
 *
 * hash
 *
 * @method int hSet($key, $hashKey, $value)
 * @method bool hSetNx($key, $hashKey, $value)
 * @method string hGet($key, $hashKey)
 * @method int hLen($key)
 * @method int hDel($key, $hashKey1, $hashKey2 = null, $hashKeyN = null)
 * @method array hKeys($key)
 * @method array hVals($key)
 * @method array hGetAll($key)
 * @method bool hExists($key, $hashKey)
 * @method bool hIncrBy($key, $hashKey, $value)
 * @method bool hIncrByFloat($key, $field, $increment)
 * @method bool hMset($key, $hashKeys)
 * @method array hMGet($key, $hashKeys)
 *
 * list
 *
 * @method array brPop(array $keys, $timeout)
 * @method array blPop(array $keys, $timeout)
 * @method int lLen($key)
 * @method int lPush($key, $value1, $value2 = null, $valueN = null)
 * @method string lPop($key)
 * @method array lRange($key, $start, $end)
 * @method int lRem($key, $value, $count)
 * @method bool lSet($key, $index, $value)
 * @method int rPush($key, $value1, $value2 = null, $valueN = null)
 * @method string rPop($key)
 *
 * set
 *
 * @method int sAdd($key, $value1, $value2 = null, $valueN = null)
 * @method array|bool scan(&$iterator, $pattern = null, $count = 0)
 * @method int sCard($key)
 * @method array sDiff($key1, $key2, $keyN = null)
 * @method array sInter($key1, $key2, $keyN = null)
 * @method int sInterStore($dstKey, $key1, $key2, $keyN = null)
 * @method int sDiffStore($dstKey, $key1, $key2, $keyN = null)
 * @method array sMembers($key)
 * @method bool sMove($srcKey, $dstKey, $member)
 * @method bool sPop($key)
 * @method string|array sRandMember($key, $count = null)
 * @method int sRem($key, $member1, $member2 = null, $memberN = null)
 * @method array sUnion($key1, $key2, $keyN = null)
 * @method int sUnionStore($dstKey, $key1, $key2, $keyN = null)
 *
 * sort
 *
 * @method int zAdd($key, $score1, $value1, $score2 = null, $value2 = null, $scoreN = null, $valueN = null)
 * @method array zRange($key, $start, $end, $withscores = null)
 * @method int zRem($key, $member1, $member2 = null, $memberN = null)
 * @method array zRevRange($key, $start, $end, $withscore = null)
 * @method array zRangeByScore($key, $start, $end, array $options = array())
 * @method array zRangeByLex($key, $min, $max, $offset = null, $limit = null)
 * @method int zCount($key, $start, $end)
 * @method int zRemRangeByScore($key, $start, $end)
 * @method int zRemRangeByRank($key, $start, $end)
 * @method int zCard($key)
 * @method float zScore($key, $member)
 * @method int zRank($key, $member)
 * @method float zIncrBy($key, $value, $member)
 * @method int zUnion($Output, $ZSetKeys, array $Weights = null, $aggregateFunction = 'SUM')
 * @method int zInter($Output, $ZSetKeys, array $Weights = null, $aggregateFunction = 'SUM')
 *
 * pub/sub
 *
 * @method int publish($channel, $message)
 * @method string|array psubscribe($patterns, $callback)
 * @method string|array subscribe($channels, $callback)
 * @method array|int pubsub($keyword, $argument)
 *
 * script
 *
 * @method mixed eval($script, $args = array(), $numKeys = 0)
 * @method mixed evalSha($scriptSha, $args = array(), $numKeys = 0)
 * @method mixed script($command, $script)
 * @method string getLastError()
 * @method bool clearLastError()
 *
 * @Bean()
 * @uses      CacheRedis
 * @version   2017年12月24日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class CacheRedis implements CacheInterface
{

    /**
     * Get the value related to the specified key
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return string|bool
     */
    public function get($key, $default = null)
    {
        $result = $this->call('get', [$key]);
        if ($result === false || $result === null) {
            return $default;
        }

        return $result;
    }

    /**
     * Set the string value in argument as value of the key.
     *
     * @param string $key
     * @param mixed  $value
     * @param int    $ttl
     *
     * @return bool
     */
    public function set($key, $value, $ttl = null)
    {
        $ttl = $this->getTtl($ttl);
        $params = ($ttl ==0)?[$key, $value]:[$key, $value, $ttl];
        return $this->call('set', $params);
    }

    /**
     * Remove specified keys.
     *
     * @param string $key
     *
     * @return int Number of keys deleted.
     */
    public function delete($key)
    {
        return $this->call('del', [$key]);
    }

    /**
     * Removes all entries from the current database.
     *
     * @return  bool  Always TRUE.
     */
    public function clear()
    {
        return $this->call('flushDB', []);
    }

    /**
     * Returns the values of all specified keys.
     *
     * For every key that does not hold a string value or does not exist,
     * the special value false is returned. Because of this, the operation never fails.
     *
     * @param iterable $keys
     * @param mixed    $default
     *
     * @return array
     */
    public function getMultiple($keys, $default = null)
    {
        $result = $this->call('mget', [$keys]);
        if ($result === false) {
            return $default;
        }

        return $result;
    }

    /**
     * Sets multiple key-value pairs in one atomic command.
     *
     * @param iterable $values
     * @param int      $ttl
     *
     * @return bool TRUE in case of success, FALSE in case of failure.
     */
    public function setMultiple($values, $ttl = null)
    {
        $result = $this->call('mset', [$values]);
        return $result;
    }

    /**
     * Remove specified keys.
     *
     * @param iterable $keys
     *
     * @return int Number of keys deleted.
     */
    public function deleteMultiple($keys)
    {
        return $this->call('del', [$keys]);
    }

    /**
     * Verify if the specified key exists.
     *
     * @param string $key
     *
     * @return  bool  If the key exists, return TRUE, otherwise return FALSE.
     */
    public function has($key)
    {
        return $this->call('exists', [$key]);
    }

    /**
     * defer call
     *
     * @param string $method
     * @param array  $params
     *
     * @return \Swoft\Cache\CacheResult
     */
    public function deferCall(string $method, array $params)
    {
        $connectPool = App::getPool(RedisPool::class);

        /* @var $client RedisConnect */
        $client = $connectPool->getConnect();
        $client->setDefer();
        $result = $client->$method(...$params);

        return new CacheResult($connectPool, $client, "", $result);
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
        return self::call($method, $arguments);
    }

    /**
     * call method by redis client
     *
     * @param string $method
     * @param array  $params
     *
     * @return mixed
     */
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

    /**
     * the ttl
     *
     * @param $ttl
     *
     * @return int
     */
    private function getTtl($ttl)
    {
        return ($ttl == null) ? 0 : (int)$ttl;
    }

}