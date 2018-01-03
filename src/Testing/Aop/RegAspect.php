<?php

namespace Swoft\Testing\Aop;

use Swoft\Aop\ProceedingJoinPoint;
use Swoft\Bean\Annotation\Around;
use Swoft\Bean\Annotation\Aspect;
use Swoft\Bean\Annotation\PointExecution;

/**
 * @Aspect()
 * @PointExecution(
 *     include={
 *      "Swoft\Testing\Aop\RegBean::reg.*",
 *     }
 * )
 *
 * @uses      RegAspect
 * @version   2017年12月27日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class RegAspect
{
    /**
     * @Around()
     */
    public function around(ProceedingJoinPoint $proceedingJoinPoint){
        $tag = ' RegAspect around before ';
        $result = $proceedingJoinPoint->proceed();
        $tag .= ' RegAspect around after ';
        return $result.$tag;
    }
}