<?php

namespace Swoft\Db\Bean\Parser;

use Swoft\Bean\Parser\AbstractParser;
use Swoft\Db\Bean\Annotation\Entity;
use Swoft\Db\Bean\Collector\EntityCollector;

/**
 * Entity注解解析器
 *
 * @uses      EntityParser
 * @version   2017年09月04日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class EntityParser extends AbstractParser
{
    /**
     * Entity注解解析
     *
     * @param string $className
     * @param Entity $objectAnnotation
     * @param string $propertyName
     * @param string $methodName
     * @param null   $propertyValue
     * @return null
     */
    public function parser(
        string $className,
        $objectAnnotation = null,
        string $propertyName = '',
        string $methodName = '',
        $propertyValue = null
    ) {
        EntityCollector::collect($className, $objectAnnotation, $propertyName, $methodName, $propertyValue);
        return null;
    }
}
