<?php

namespace Swoft\Bean\Parser;

use Swoft\Bean\Collector;
use Swoft\Bean\Annotation\Middleware;


/**
 * @uses      MiddlewareParser
 * @version   2017-11-17
 * @author    huangzhhui <huangzhwork@gmail.com>
 * @copyright Copyright 2010-2017 Swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class MiddlewareParser extends AbstractParser
{

    /**
     * Parse middleware annotation
     *
     * @param string      $className
     * @param Middleware  $objectAnnotation
     * @param string      $propertyName
     * @param string      $methodName
     * @param string|null $propertyValue
     *
     * @return mixed
     */
    public function parser(
        string $className,
        $objectAnnotation = null,
        string $propertyName = "",
        string $methodName = "",
        $propertyValue = null
    ) {
        $middlewares = [
            $objectAnnotation->getClass()
        ];

        if ($methodName) {
            $scanMiddlewares = Collector::$requestMapping[$className]['middlewares']['actions'][$methodName]??[];
            Collector::$requestMapping[$className]['middlewares']['actions'][$methodName] = array_merge($middlewares, $scanMiddlewares);
        } else {
            $scanMiddlewares = Collector::$requestMapping[$className]['middlewares']['group']??[];
            Collector::$requestMapping[$className]['middlewares']['group'] = array_merge($middlewares, $scanMiddlewares);
        }
        return null;
    }
}