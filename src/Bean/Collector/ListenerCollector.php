<?php

namespace Swoft\Bean\Collector;

use Swoft\Bean\Annotation\Listener;
use Swoft\Bean\CollectorInterface;

/**
 * the collector of listener
 *
 * @uses      ListenerCollector
 * @version   2018年01月14日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class ListenerCollector implements CollectorInterface
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
        if($objectAnnotation instanceof Listener){
            $eventName = $objectAnnotation->getEvent();
            self::$listeners[$eventName][] = $className;
        }
    }

    /**
     * @return array
     */
    public static function getCollector()
    {
        return self::$listeners;
    }
}