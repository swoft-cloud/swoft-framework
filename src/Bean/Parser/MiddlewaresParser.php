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
     * @param string      $className
     * @param Middlewares $objectAnnotation
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
        $middlewares = [];
        foreach ($objectAnnotation->getMiddlewares() as $middleware) {
            if ($middleware instanceof Middleware) {
                $middlewares[] = $middleware->getClass();
            }
        }
        $middlewares = array_unique($middlewares);

        if(isset(Collector::$requestMapping[$className]) && !empty($methodName)){
            $scanMiddlewares = Collector::$requestMapping[$className]['middlewares']['actions'][$methodName]??[];
            Collector::$requestMapping[$className]['middlewares']['actions'][$methodName] = array_merge($scanMiddlewares, $middlewares);
            return null;
        }

        if(isset(Collector::$requestMapping[$className]) && empty($methodName)){
            $scanMiddlewares = Collector::$requestMapping[$className]['middlewares']['group']??[];
            Collector::$requestMapping[$className]['middlewares']['group'] = array_merge($scanMiddlewares, $middlewares);
            return null;
        }

        if(isset(Collector::$serviceMapping[$className]) && !empty($methodName)){
            $scanMiddlewares = Collector::$serviceMapping[$className]['middlewares']['actions'][$methodName]??[];
            Collector::$serviceMapping[$className]['middlewares']['actions'][$methodName] = array_merge($scanMiddlewares, $middlewares);
            return null;
        }

        if(isset(Collector::$serviceMapping[$className]) && empty($methodName)){
            $scanMiddlewares = Collector::$serviceMapping[$className]['middlewares']['group']??[];
            Collector::$serviceMapping[$className]['middlewares']['group'] = array_merge($scanMiddlewares, $middlewares);
            return null;
        }

        return null;
    }
}