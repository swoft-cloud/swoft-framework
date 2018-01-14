<?php

namespace Swoft\Bean\Collector;

use Swoft\Bean\Annotation\Breaker;
use Swoft\Bean\CollectorInterface;

/**
 * the collector of breaker
 *
 * @uses      BreakerCollector
 * @version   2018年01月14日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class BreakerCollector implements CollectorInterface
{
    /**
     * @var array
     */
    private static $breakers = [];

    /**
     * @param string $className
     * @param null   $objectAnnotation
     * @param string $propertyName
     * @param string $methodName
     * @param null   $propertyValue
     */
    public static function collect(string $className, $objectAnnotation = null, string $propertyName = "", string $methodName = "", $propertyValue = null)
    {
        if($objectAnnotation instanceof  Breaker){
            $breakerName = $objectAnnotation->getName();
            $breakerName = empty($breakerName) ? $className : $breakerName;

            self::$breakers[$breakerName] = $className;
        }
    }

    /**
     * @return array
     */
    public static function getCollector()
    {
        return self::$breakers;
    }

}