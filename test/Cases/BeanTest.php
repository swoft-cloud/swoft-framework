<?php

namespace Swoft\Test\Cases;

use PHPUnit\Framework\TestCase;
use Swoft\Proxy\Proxy;
use Swoft\Test\Testing\Bean\ProxyTest;
use Swoft\Test\Testing\Bean\TestHandler;

/**
 * the test of bean
 *
 * @uses      BeanTest
 * @version   2017年12月23日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class BeanTest extends TestCase
{
    public function testProxy()
    {
        $object  = new ProxyTest(1, 2);
        $handler = new TestHandler($object);

        /* @var ProxyTest $proxy */
        $proxy = Proxy::newProxyInstance(ProxyTest::class, $handler);
        $this->assertEquals('p11beforeafter', $proxy->publicParams('p1', 'p2'));
        $this->assertEquals('p1p20beforeafter', $proxy->publicFun1('p1', 'p2'));
        $this->assertEquals('p1p23beforeafter', $proxy->publicFun1('p1', 'p2', [1, 2, 3]));
        $this->assertEquals('p1p21beforeafter', $proxy->publicFun2('p1', 'p2'));
        $this->assertEquals('p1p21beforeafter', $proxy->publicFun3('p1', 'p2'));
        $this->assertEquals('p1p25beforeafter', $proxy->publicFun3('p1', 'p2', 5));
        $this->assertEquals('p1p2beforeafter', $proxy->publicFun1Base('p1', 'p2'));
        $this->assertEquals('p1p21.6beforeafter', $proxy->publicFun2Base('p1', 'p2', 1.6));
        $this->assertEquals('p1p2beforeafter', $proxy->publicFun1Trait('p1', 'p2'));
        $this->assertEquals('p1p2beforeafter', $proxy->publicFun2Trait('p1', 'p2'));
        $this->assertEquals('p1p2beforeafter', $proxy->publicFun3Trait('p1', 'p2'));
    }
}
