<?php

namespace Swoft\Test;

use Swoft\App;
use Swoft\Bean\Collector;
use Swoft\Testing\Aop\AopBean;

/**
 *
 *
 * @uses      AopTest
 * @version   2017年12月24日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class AopTest extends AbstractTestCase
{
    public function testAllAdvice()
    {
        /* @var \Swoft\Testing\Aop\AopBean $aopBean*/
        $aopBean = App::getBean(AopBean::class);
        $result = $aopBean->doAop();
        $this->assertEquals('do aop around-before2  before2  around-after2  afterReturn2  around-before1  before1  around-after1  afterReturn1 ', $result);
    }
}