<?php

namespace Swoft\Bean\Parser;

use Swoft\Bean\Annotation\BootProcess;
use Swoft\Bean\Annotation\Scope;
use Swoft\Bean\Collector\BootProcessCollector;

/**
 * the parser of bootstrap process
 *
 * @uses      BootProcessParser
 * @version   2018年01月12日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class BootProcessParser extends AbstractParserInterface
{
    /**
     * @param string      $className
     * @param BootProcess $objectAnnotation
     * @param string      $propertyName
     * @param string      $methodName
     * @param mixed       $propertyValue
     *
     * @return array
     */
    public function parser(string $className, $objectAnnotation = null, string $propertyName = "", string $methodName = "", $propertyValue = null)
    {
        $beanName = $className;
        $scope    = Scope::PROTOTYPE;

        BootProcessCollector::collect($className, $objectAnnotation, $propertyName, $methodName, $propertyValue);

        return [$beanName, $scope, ""];
    }
}