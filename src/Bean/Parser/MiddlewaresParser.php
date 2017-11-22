<?php

namespace Swoft\Bean\Parser;

use Swoft\Bean\Annotation\Middleware;
use Swoft\Bean\Annotation\Middlewares;
use Swoft\Bean\Collector;


/**
 * @uses      MiddlewaresParser
 * @version   2017-11-17
 * @author    huangzhhui <huangzhwork@gmail.com>
 * @copyright Copyright 2010-2017 Swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class MiddlewaresParser extends AbstractParser
{

    /**
     * Parse middlewares annotation
     *
     * @param string $className
     * @param object $objectAnnotation
     * @param string $propertyName
     * @param string $methodName
     * @param string|null $propertyValue
     * @return mixed
     */
    public function parser(
        string $className,
        $objectAnnotation = null,
        string $propertyName = "",
        string $methodName = "",
        $propertyValue = null
    ) {
        $middlewares = [];
        if ($objectAnnotation instanceof Middlewares) {
            foreach ($objectAnnotation->getMiddlewares() as $middleware) {
                if ($middleware instanceof Middleware) {
                    $middlewares[] = $middleware->getClass();
                }
            }
            $middlewares = array_unique($middlewares);
        }
        if ($methodName) {
            Collector::$requestMapping[$className]['middlewares']['actions'][$methodName] = $middlewares;
        } else {
            Collector::$requestMapping[$className]['middlewares']['group'] = $middlewares;
        }
        return null;
    }
}