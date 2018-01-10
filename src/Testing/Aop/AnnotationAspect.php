<?php

namespace Swoft\Testing\Aop;

use Swoft\Aop\ProceedingJoinPoint;
use Swoft\Bean\Annotation\Around;
use Swoft\Bean\Annotation\Aspect;
use Swoft\Bean\Annotation\PointAnnotation;
use Swoft\Bean\Annotation\Cacheable;
use Swoft\Bean\Annotation\CachePut;

/**
 * @Aspect()
 * @PointAnnotation(
 *     include={
 *      Cacheable::class,
 *      CachePut::class
 *      }
 *  )
 * @uses      AnnotationAspect
 * @version   2017年12月27日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class AnnotationAspect
{
    /**
     * @Around()
     */
    public function around(ProceedingJoinPoint $proceedingJoinPoint)
    {
        $tag = ' around before ';
        $result = $proceedingJoinPoint->proceed();
        $tag .= ' around after ';
        return $result.$tag;
    }
}
