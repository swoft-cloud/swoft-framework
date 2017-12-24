<?php

namespace Swoft\Bean\Parser;

use Swoft\Bean\Annotation\After;
use Swoft\Bean\Collector;

/**
 * the before advice of parser
 *
 * @uses      AfterParser
 * @version   2017年12月24日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class AfterParser extends AbstractParser
{
    /**
     * after parsing
     *
     * @param string $className
     * @param After  $objectAnnotation
     * @param string $propertyName
     * @param string $methodName
     *
     * @return mixed
     */
    public function parser(string $className, $objectAnnotation = null, string $propertyName = "", string $methodName = "", $propertyValue = null)
    {
        if (!isset(Collector::$aspects[$className])) {
            return null;
        }

        Collector::$aspects[$className]['advice']['after'] = $methodName;

        return null;
    }
}