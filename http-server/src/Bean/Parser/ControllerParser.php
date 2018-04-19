<?php

namespace Swoft\Http\Server\Bean\Parser;

use Swoft\Bean\Parser\AbstractParser;
use Swoft\Http\Server\Bean\Annotation\Controller;
use Swoft\Bean\Annotation\Scope;
use Swoft\Http\Server\Bean\Collector\ControllerCollector;

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
    public function parser(string $className, $objectAnnotation = null, string $propertyName = '', string $methodName = '', $propertyValue = null): array
    {
        $beanName = $className;
        $scope = Scope::SINGLETON;

        // collect controller
        ControllerCollector::collect($className, $objectAnnotation, $propertyName, $methodName, $propertyValue);

        return [$beanName, $scope, ''];
    }
}
