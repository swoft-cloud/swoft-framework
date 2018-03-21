<?php

namespace Swoft\Db\Bean\Parser;

use Swoft\Bean\Parser\AbstractParser;
use Swoft\Db\Bean\Collector\BuilderCollector;

/**
 * BuilderParser
 */
class BuilderParser extends AbstractParser
{
    /**
     * @param string $className
     * @param null   $objectAnnotation
     * @param string $propertyName
     * @param string $methodName
     * @param null   $propertyValue
     *
     * @return null
     */
    public function parser(
        string $className,
        $objectAnnotation = null,
        string $propertyName = '',
        string $methodName = '',
        $propertyValue = null
    ) {
        BuilderCollector::collect($className, $objectAnnotation, $propertyName, $methodName, $propertyValue);

        return null;
    }
}