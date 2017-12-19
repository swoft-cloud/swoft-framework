<?php

namespace Swoft\Test\Pool;

use Swoft\App;
use Swoft\Pool\ProviderSelector;
use Swoft\Test\AbstractTestCase;
use Swoft\Testing\Pool\Config\ConsulEnvConfig;
use Swoft\Testing\Pool\Config\ConsulPptConfig;
use Swoft\Testing\Pool\Config\DbEnvPoolConfig;
use Swoft\Testing\Pool\Config\DbPptPoolConfig;
use Swoft\Testing\Pool\Config\DbSlaveEnvPoolConfig;
use Swoft\Testing\Pool\Config\DbSlavePptConfig;
use Swoft\Testing\Pool\Config\EnvAndPptPoolConfig;
use Swoft\Testing\Pool\Config\EnvPoolConfig;
use Swoft\Testing\Pool\Config\PartEnvPoolConfig;
use Swoft\Testing\Pool\Config\PartPoolConfig;
use Swoft\Testing\Pool\Config\PropertyPoolConfig;
use Swoft\Testing\Pool\Config\RedisEnvPoolConfig;
use Swoft\Testing\Pool\Config\RedisPptPoolConfig;

/**
 * pool test
 *
 * @uses      PoolTest
 * @version   2017年12月16日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class PoolTest extends AbstractTestCase
{
    public function testPoolConfigByProperties()
    {
        /* @var \Swoft\Pool\PoolProperties $pConfig */
        $pConfig = App::getBean(PropertyPoolConfig::class);
        $this->assertEquals($pConfig->getName(), 'test');
        $this->assertEquals($pConfig->getProvider(), 'p');
        $this->assertEquals($pConfig->getTimeout(), 1);
        $this->assertEquals($pConfig->getUri(), [
            '127.0.0.1:6379',
            '127.0.0.1:6378',
        ]);
        $this->assertEquals($pConfig->getBalancer(), 'b');
        $this->assertEquals($pConfig->getMaxActive(), 1);
        $this->assertEquals($pConfig->getMaxIdel(), 1);
        $this->assertEquals($pConfig->isUseProvider(), true);
        $this->assertEquals($pConfig->getMaxWait(), 1);
    }

    public function testPartConfigByProperties()
    {
        /* @var \Swoft\Pool\PoolProperties $pConfig */
        $pConfig = App::getBean(PartPoolConfig::class);
        $this->assertEquals($pConfig->getName(), 'test');
        $this->assertEquals($pConfig->getProvider(), ProviderSelector::TYPE_CONSUL);
        $this->assertEquals($pConfig->getTimeout(), 200);
        $this->assertEquals($pConfig->getUri(), []);
        $this->assertEquals($pConfig->getBalancer(), 'b');
        $this->assertEquals($pConfig->getMaxActive(), 1);
        $this->assertEquals($pConfig->getMaxIdel(), 1);
        $this->assertEquals($pConfig->isUseProvider(), false);
        $this->assertEquals($pConfig->getMaxWait(), 1);
    }

    public function testPoolConfigEnv()
    {
        /* @var \Swoft\Pool\PoolProperties $pConfig */
        $pConfig = App::getBean(EnvPoolConfig::class);
        $this->assertEquals($pConfig->getName(), 'test');
        $this->assertEquals($pConfig->getProvider(), 'c1');
        $this->assertEquals($pConfig->getTimeout(), 2);
        $this->assertEquals($pConfig->getUri(), [
            '127.0.0.1:6378',
        ]);
        $this->assertEquals($pConfig->getBalancer(), 'r1');
        $this->assertEquals($pConfig->getMaxActive(), 2);
        $this->assertEquals($pConfig->getMaxIdel(), 2);
        $this->assertEquals($pConfig->isUseProvider(), true);
        $this->assertEquals($pConfig->getMaxWait(), 2);
    }

    public function testPoolConfigEnvPart()
    {
        /* @var \Swoft\Pool\PoolProperties $pConfig */
        $pConfig = App::getBean(PartEnvPoolConfig::class);
        $this->assertEquals($pConfig->getName(), 'test');
        $this->assertEquals($pConfig->getProvider(), 'c1');
        $this->assertEquals($pConfig->getTimeout(), 2);
        $this->assertEquals($pConfig->getUri(), [
            '127.0.0.1:6378',
        ]);
        $this->assertEquals($pConfig->getBalancer(), 'random');
        $this->assertEquals($pConfig->getMaxActive(), 2);
        $this->assertEquals($pConfig->getMaxIdel(), 6);
        $this->assertEquals($pConfig->isUseProvider(), false);
        $this->assertEquals($pConfig->getMaxWait(), 100);
    }

    public function testPoolConfigEnvAndEnv()
    {
        /* @var \Swoft\Pool\PoolProperties $pConfig */
        $pConfig = App::getBean(EnvAndPptPoolConfig::class);
        $this->assertEquals($pConfig->getName(), 'test');
        $this->assertEquals($pConfig->getProvider(), 'c1');
        $this->assertEquals($pConfig->getTimeout(), 2);
        $this->assertEquals($pConfig->getUri(), [
            '127.0.0.1:6378',
        ]);
        $this->assertEquals($pConfig->getBalancer(), 'r1');
        $this->assertEquals($pConfig->getMaxActive(), 2);
        $this->assertEquals($pConfig->getMaxIdel(), 2);
        $this->assertEquals($pConfig->isUseProvider(), true);
        $this->assertEquals($pConfig->getMaxWait(), 2);
    }

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

    public function testDbPpt()
    {
        /* @var \Swoft\Pool\PoolProperties $pConfig */
        $pConfig = App::getBean(DbPptPoolConfig::class);
        $this->assertEquals($pConfig->getName(), 'master1');
        $this->assertEquals($pConfig->getProvider(), 'consul1');
        $this->assertEquals($pConfig->getTimeout(), 1);
        $this->assertEquals($pConfig->getUri(), [
            '127.0.0.1:3301',
            '127.0.0.1:3301',
        ]);
        $this->assertEquals($pConfig->getBalancer(), 'random1');
        $this->assertEquals($pConfig->getMaxActive(), 1);
        $this->assertEquals($pConfig->getMaxIdel(), 1);
        $this->assertEquals($pConfig->isUseProvider(), true);
        $this->assertEquals($pConfig->getMaxWait(), 1);
    }

    public function testDbEnv()
    {
        /* @var \Swoft\Pool\PoolProperties $pConfig */
        $pConfig = App::getBean(DbEnvPoolConfig::class);
        $this->assertEquals($pConfig->getName(), 'master2');
        $this->assertEquals($pConfig->getProvider(), 'consul2');
        $this->assertEquals($pConfig->getTimeout(), 2);
        $this->assertEquals($pConfig->getUri(), [
            '127.0.0.1:3302',
            '127.0.0.1:3302',
        ]);
        $this->assertEquals($pConfig->getBalancer(), 'random2');
        $this->assertEquals($pConfig->getMaxActive(), 2);
        $this->assertEquals($pConfig->getMaxIdel(), 2);
        $this->assertEquals($pConfig->isUseProvider(), true);
        $this->assertEquals($pConfig->getMaxWait(), 2);
    }

    public function testDbSlavePpt()
    {
        /* @var \Swoft\Pool\PoolProperties $pConfig */
        $pConfig = App::getBean(DbSlavePptConfig::class);
        $this->assertEquals($pConfig->getName(), 'slave1');
        $this->assertEquals($pConfig->getProvider(), 'consul1');
        $this->assertEquals($pConfig->getTimeout(), 1);
        $this->assertEquals($pConfig->getUri(), [
            '127.0.0.1:3301',
            '127.0.0.1:3301',
        ]);
        $this->assertEquals($pConfig->getBalancer(), 'random1');
        $this->assertEquals($pConfig->getMaxActive(), 1);
        $this->assertEquals($pConfig->getMaxIdel(), 1);
        $this->assertEquals($pConfig->isUseProvider(), true);
        $this->assertEquals($pConfig->getMaxWait(), 1);
    }

    public function testDbSlaveEnv()
    {
        /* @var \Swoft\Pool\PoolProperties $pConfig */
        $pConfig = App::getBean(DbSlaveEnvPoolConfig::class);
        $this->assertEquals($pConfig->getName(), 'slave2');
        $this->assertEquals($pConfig->getProvider(), 'consul2');
        $this->assertEquals($pConfig->getTimeout(), 2);
        $this->assertEquals($pConfig->getUri(), [
            '127.0.0.1:3302',
            '127.0.0.1:3302',
        ]);
        $this->assertEquals($pConfig->getBalancer(), 'random2');
        $this->assertEquals($pConfig->getMaxActive(), 2);
        $this->assertEquals($pConfig->getMaxIdel(), 2);
        $this->assertEquals($pConfig->isUseProvider(), true);
        $this->assertEquals($pConfig->getMaxWait(), 2);
    }

    public function testConsulPpt()
    {
        /* @var \Swoft\Testing\Pool\Config\ConsulPptConfig $pConfig */
        $pConfig = App::getBean(ConsulPptConfig::class);
        $this->assertEquals('http://127.0.0.1:81', $pConfig->getAddress());
        $this->assertEquals(1, $pConfig->getTimeout());
        $this->assertEquals(1, $pConfig->getInterval());
        $this->assertEquals(['1'], $pConfig->getTags());
    }

    public function testConsulEnv()
    {
        /* @var \Swoft\Testing\Pool\Config\ConsulPptConfig $pConfig */
        $pConfig = App::getBean(ConsulEnvConfig::class);
        $this->assertEquals('http://127.0.0.1:82', $pConfig->getAddress());
        $this->assertEquals(2, $pConfig->getTimeout());
        $this->assertEquals(2, $pConfig->getInterval());
        $this->assertEquals([1,2], $pConfig->getTags());
    }
}