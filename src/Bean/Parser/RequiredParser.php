<?php

namespace Swoft\Bean\Parser;

use Swoft\Bean\Annotation\Required;
use Swoft\Bean\Collector\EntityCollector;

/**
 * Required注解解析器
 *
 * @uses      RequiredParser
 * @version   2017年09月05日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class RequiredParser extends AbstractParserInterface
{
    /**
     * Required注解解析
     *
     * @param string $className
     * @param Required $objectAnnotation
     * @param string $propertyName
     * @param string $methodName
     * @param null $propertyValue
     * @return mixed
     */
    public function parser(string $className, $objectAnnotation = null, string $propertyName = "", string $methodName = "", $propertyValue = null)
    {
        EntityCollector::collect($className, $objectAnnotation, $propertyName, $methodName, $propertyValue);
        return null;
    }
}
