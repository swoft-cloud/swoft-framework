<?php

namespace Swoft\Bean\Collector;

use Swoft\Bean\Annotation\BootProcess;
use Swoft\Bean\CollectorInterface;

/**
 * the collector of bootstrap process
 *
 * @uses      BootProcessCollector
 * @version   2018年01月12日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class BootProcessCollector implements CollectorInterface
{
    /**
     * @var array
     */
    private static $bootProcess = [];

    /**
     * @param string $className
     * @param object $objectAnnotation
     * @param string $propertyName
     * @param string $methodName
     * @param null   $propertyValue
     */
    public static function collect(string $className, $objectAnnotation = null, string $propertyName = "", string $methodName = "", $propertyValue = null)
    {
        if ($objectAnnotation instanceof BootProcess) {

            $num  = $objectAnnotation->getNum();
            $name = $objectAnnotation->getName();
            $name = empty($name) ? $className : $name;

            self::$bootProcess[$className]['num']  = $num;
            self::$bootProcess[$className]['name'] = $name;


            return;
        }
    }

    /**
     * @return array
     */
    public static function getCollector()
    {
        return self::$bootProcess;
    }

}