<?php

namespace Swoft\Test\Cases;

use PHPUnit\Framework\TestCase;
use Swoft\App;
use Swoft\Test\Testing\Pool\ConsulEnvConfig;
use Swoft\Test\Testing\Pool\ConsulPptConfig;
use Swoft\Test\Testing\Pool\EnvAndPptFromPptPoolConfig;
use Swoft\Test\Testing\Pool\EnvAndPptPoolConfig;
use Swoft\Test\Testing\Pool\EnvPoolConfig;
use Swoft\Test\Testing\Pool\PartEnvPoolConfig;
use Swoft\Test\Testing\Pool\PartPoolConfig;
use Swoft\Test\Testing\Pool\PropertyPoolConfig;

/**
 * Class PoolTest
 *
 * @package Swoft\Test\Cases
 */
class PoolTest extends TestCase
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
        $this->assertEquals($pConfig->isUseProvider(), true);
        $this->assertEquals($pConfig->getMaxWait(), 1);
    }

    public function testPartConfigByProperties()
    {
        /* @var \Swoft\Pool\PoolProperties $pConfig */
        $pConfig = App::getBean(PartPoolConfig::class);
        $this->assertEquals($pConfig->getName(), 'test');
        $this->assertEquals($pConfig->getProvider(), 'consul');
        $this->assertEquals($pConfig->getTimeout(), 3);
        $this->assertEquals($pConfig->getUri(), []);
        $this->assertEquals($pConfig->getBalancer(), 'b');
        $this->assertEquals($pConfig->getMaxActive(), 1);
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
        $this->assertEquals($pConfig->isUseProvider(), false);
        $this->assertEquals($pConfig->getMaxWait(), 20);
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
        $this->assertEquals($pConfig->isUseProvider(), true);
        $this->assertEquals($pConfig->getMaxWait(), 2);
    }

    public function testPoolConfigEnvAndConfig()
    {
        /* @var \Swoft\Pool\PoolProperties $pConfig */
        $pConfig = App::getBean(EnvAndPptFromPptPoolConfig::class);
        $this->assertEquals($pConfig->getName(), 'test2');
        $this->assertEquals($pConfig->getProvider(), 'p2');
        $this->assertEquals($pConfig->getTimeout(), 2);
        $this->assertEquals($pConfig->getUri(), [
            '127.0.0.1:6379',
            '127.0.0.1:6378',
        ]);
        $this->assertEquals($pConfig->getBalancer(), 'b2');
        $this->assertEquals($pConfig->getMaxActive(), 2);
        $this->assertEquals($pConfig->isUseProvider(), true);
        $this->assertEquals($pConfig->getMaxWait(), 2);
    }

    public function testConsulPpt()
    {
        /* @var ConsulPptConfig $pConfig */
        $pConfig = App::getBean(ConsulPptConfig::class);
        $this->assertEquals('http://127.0.0.1:81', $pConfig->getAddress());
        $this->assertEquals(1, $pConfig->getTimeout());
        $this->assertEquals(1, $pConfig->getInterval());
        $this->assertEquals(['1'], $pConfig->getTags());
    }

    public function testConsulEnv()
    {
        /* @var ConsulPptConfig $pConfig */
        $pConfig = App::getBean(ConsulEnvConfig::class);
        $this->assertEquals('http://127.0.0.1:82', $pConfig->getAddress());
        $this->assertEquals(2, $pConfig->getTimeout());
        $this->assertEquals(2, $pConfig->getInterval());
        $this->assertEquals([1,2], $pConfig->getTags());
    }
}