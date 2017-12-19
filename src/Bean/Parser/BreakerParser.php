<?php

namespace Swoft\Bean\Parser;

use Swoft\Bean\Annotation\Breaker;
use Swoft\Bean\Annotation\Scope;
use Swoft\Bean\Collector;

/**
 * breaker parser
 *
 * @uses      BreakerParser
 * @version   2017年12月17日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class BreakerParser extends AbstractParser
{
    /**
     * @param string  $className
     * @param Breaker $objectAnnotation
     * @param string  $propertyName
     * @param string  $methodName
     * @param null    $propertyValue
     *
     * @return array
     */
    public function parser(
        string $className,
        $objectAnnotation = null,
        string $propertyName = "",
        string $methodName = "",
        $propertyValue = null
    ) {

        $breakerName = $objectAnnotation->getName();
        $breakerName = empty($breakerName) ? $className : $breakerName;

        Collector::$breakers[$breakerName] = $className;

        return [$className, Scope::SINGLETON, ""];
    }

}