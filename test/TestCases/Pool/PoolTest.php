<?php

namespace Swoft\Test\Pool;

use Swoft\App;
use Swoft\Pool\ProviderSelector;
use Swoft\Test\AbstractTestCase;
use Swoft\Testing\Pool\Config\EnvAndPptPoolConfig;
use Swoft\Testing\Pool\Config\EnvPoolConfig;
use Swoft\Testing\Pool\Config\PartEnvPoolConfig;
use Swoft\Testing\Pool\Config\PartPoolConfig;
use Swoft\Testing\Pool\Config\PropertyPoolConfig;

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
            '127.0.0.1:6379',
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
            '127.0.0.1:6379',
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
            '127.0.0.1:6379',
        ]);
        $this->assertEquals($pConfig->getBalancer(), 'r1');
        $this->assertEquals($pConfig->getMaxActive(), 2);
        $this->assertEquals($pConfig->getMaxIdel(), 2);
        $this->assertEquals($pConfig->isUseProvider(), true);
        $this->assertEquals($pConfig->getMaxWait(), 2);
    }
}