<?php

namespace Swoft\Db\Bean\Collector;

use Swoft\Bean\CollectorInterface;
use Swoft\Db\Bean\Annotation\Builder;

/**
 * BuilderCollector
 */
class BuilderCollector implements CollectorInterface
{
    /**
     * @var array
     */
    private static $builders = [];

    /**
     * @param string $className
     * @param null   $objectAnnotation
     * @param string $propertyName
     * @param string $methodName
     * @param null   $propertyValue
     *
     * @return  void
     */
    public static function collect(
        string $className,
        $objectAnnotation = null,
        string $propertyName = '',
        string $methodName = '',
        $propertyValue = null
    ) {
        if ($objectAnnotation instanceof Builder) {

            $driver                  = $objectAnnotation->getDriver();
            self::$builders[$driver] = $className;
        }
    }

    /**
     * @return array
     */
    public static function getCollector()
    {
        return self::$builders;
    }
}