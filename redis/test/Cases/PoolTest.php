<?php

namespace SwoftTest\Redis;

use Swoft\App;
use SwoftTest\Redis\Pool\RedisEnvPoolConfig;
use SwoftTest\Redis\Pool\RedisPptPoolConfig;

/**
 *
 *
 * @uses      PoolTest
 * @version   2018年01月25日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class PoolTest extends AbstractTestCase
{
    public function testRedisPoolPpt()
    {
        /* @var \Swoft\Pool\PoolProperties $pConfig */
        $pConfig = App::getBean(RedisPptPoolConfig::class);
        $this->assertEquals($pConfig->getName(), 'redis1');
        $this->assertEquals($pConfig->getProvider(), 'consul1');
        $this->assertEquals($pConfig->getTimeout(), 1);
        $this->assertEquals($pConfig->getUri(), [
            '127.0.0.1:1111',
            '127.0.0.1:1111',
        ]);
        $this->assertEquals($pConfig->getBalancer(), 'random1');
        $this->assertEquals($pConfig->getMaxActive(), 1);
        $this->assertEquals($pConfig->getMaxIdel(), 1);
        $this->assertEquals($pConfig->isUseProvider(), true);
        $this->assertEquals($pConfig->getMaxWait(), 1);
    }

    public function testRedisPoolEnv()
    {
        /* @var \Swoft\Pool\PoolProperties $pConfig */
        $pConfig = App::getBean(RedisEnvPoolConfig::class);
        $this->assertEquals($pConfig->getName(), 'redis2');
        $this->assertEquals($pConfig->getProvider(), 'consul2');
        $this->assertEquals($pConfig->getTimeout(), 2);
        $this->assertEquals($pConfig->getUri(), [
            '127.0.0.1:2222',
            '127.0.0.1:2222',
        ]);
        $this->assertEquals($pConfig->getBalancer(), 'random2');
        $this->assertEquals($pConfig->getMaxActive(), 2);
        $this->assertEquals($pConfig->getMaxIdel(), 2);
        $this->assertEquals($pConfig->isUseProvider(), true);
        $this->assertEquals($pConfig->getMaxWait(), 2);
    }
}