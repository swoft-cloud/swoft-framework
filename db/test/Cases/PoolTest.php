<?php

namespace SwoftTest\Db;

use PHPUnit\Framework\TestCase;
use Swoft\App;
use SwoftTest\Db\Pool\DbEnvPoolConfig;
use SwoftTest\Db\Pool\DbPptPoolConfig;
use SwoftTest\Db\Pool\DbSlaveEnvPoolConfig;
use SwoftTest\Db\Pool\DbSlavePptConfig;

/**
 * PoolTest
 */
class PoolTest extends TestCase
{
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
            '127.0.0.1:3306/test?user=root&password=123456&charset=utf8',
            '127.0.0.1:3306/test?user=root&password=123456&charset=utf8',
        ]);
        $this->assertEquals($pConfig->getBalancer(), 'random');
        $this->assertEquals($pConfig->getMaxActive(), 30);
        $this->assertEquals($pConfig->isUseProvider(), false);
        $this->assertEquals($pConfig->getMaxWait(), 10);
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
        $this->assertEquals($pConfig->getTimeout(), 3);
        $this->assertEquals($pConfig->getUri(), [
            '127.0.0.1:3306/test?user=root&password=123456&charset=utf8',
            '127.0.0.1:3306/test?user=root&password=123456&charset=utf8',
        ]);
        $this->assertEquals($pConfig->getBalancer(), 'random');
        $this->assertEquals($pConfig->getMaxActive(), 30);
        $this->assertEquals($pConfig->isUseProvider(), false);
        $this->assertEquals($pConfig->getMaxWait(), 10);
    }
}