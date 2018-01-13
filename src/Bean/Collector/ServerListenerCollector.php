<?php

namespace Swoft\Bean\Collector;

use Swoft\Bean\Annotation\BeforeStart;
use Swoft\Bean\CollectorInterface;
use Swoft\Bootstrap\SwooleEvent;

/**
 * the collector of server listener
 *
 * @uses      ServerListenerCollector
 * @version   2018年01月13日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class ServerListenerCollector implements CollectorInterface
{
    /**
     * @var array
     */
    private static $listeners = [];

    /**
     * @param string $className
     * @param object   $objectAnnotation
     * @param string $propertyName
     * @param string $methodName
     * @param null   $propertyValue
     */
    public static function collect(string $className, $objectAnnotation = null, string $propertyName = "", string $methodName = "", $propertyValue = null)
    {
        if($objectAnnotation instanceof BeforeStart){
            self::$listeners[SwooleEvent::ON_BEFORE_START][] = $className;
        }
    }

    public static function getCollector()
    {
    }

}