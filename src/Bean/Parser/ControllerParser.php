<?php

namespace Swoft\Bean\Parser;

use Swoft\Bean\Annotation\Controller;
use Swoft\Bean\Annotation\Scope;
use Swoft\Bean\Collector;

/**
 * AutoController注解解析器
 *
 * @uses      ControllerParser
 * @version   2017年09月03日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class ControllerParser extends AbstractParser
{
    /**
     * AutoController注解解析
     *
     * @param string      $className
     * @param Controller  $objectAnnotation
     * @param string      $propertyName
     * @param string      $methodName
     * @param string|null $propertyValue
     *
     * @return array
     */
    public function parser(string $className, $objectAnnotation = null, string $propertyName = "", string $methodName = "", $propertyValue = null)
    {
        $beanName = $className;
        $scope = Scope::SINGLETON;
        $prefix = $objectAnnotation->getPrefix();

        // 路由收集
        Collector::$requestMapping[$className]['prefix'] = $prefix;
        return [$beanName, $scope, ""];
    }
}
