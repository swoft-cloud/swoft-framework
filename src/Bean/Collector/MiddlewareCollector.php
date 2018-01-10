<?php

namespace Swoft\Bean\Collector;

use Swoft\Bean\Annotation\Middleware;
use Swoft\Bean\Annotation\Middlewares;
use Swoft\Bean\CollectorInterface;

/**
 * the collector of middleware
 *
 * @uses      MiddlewareCollector
 * @version   2018年01月08日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class MiddlewareCollector implements CollectorInterface
{
    /**
     * @var array
     */
    private static $middlewares = [];

    /**
     * @param string $className
     * @param null   $objectAnnotation
     * @param string $propertyName
     * @param string $methodName
     * @param null   $propertyValue
     */
    public static function collect(string $className, $objectAnnotation = null, string $propertyName = "", string $methodName = "", $propertyValue = null)
    {
        if($objectAnnotation instanceof Middleware){
            self::collectMiddleware($className, $methodName, $objectAnnotation);
        }elseif($objectAnnotation instanceof Middlewares){
            self::collectMiddlewares($className, $methodName, $objectAnnotation);
        }
    }

    /**
     * @return array
     */
    public static function getCollector()
    {
        return self::$middlewares;
    }

    /**
     * collect middlewares
     *
     * @param string      $className
     * @param string      $methodName
     * @param Middlewares $middlewaresAnnotation
     */
    private static function collectMiddlewares(string $className, string $methodName, Middlewares $middlewaresAnnotation)
    {
        $middlewares = [];
        foreach ($middlewaresAnnotation->getMiddlewares() as $middleware) {
            if ($middleware instanceof Middleware) {
                $middlewares[] = $middleware->getClass();
            }
        }
        $middlewares = array_unique($middlewares);

        if(!empty($methodName)){
            $scanMiddlewares = self::$middlewares[$className]['middlewares']['actions'][$methodName]??[];
            self::$middlewares[$className]['middlewares']['actions'][$methodName] = array_merge($scanMiddlewares, $middlewares);
        }else{
            $scanMiddlewares = self::$middlewares[$className]['middlewares']['group']??[];
            self::$middlewares[$className]['middlewares']['group'] = array_merge($scanMiddlewares, $middlewares);
        }
    }

    /**
     * collect middleware
     *
     * @param string     $className
     * @param string     $methodName
     * @param Middleware $middlewareAnnotation
     */
    private static function collectMiddleware(string $className, string $methodName, Middleware $middlewareAnnotation)
    {
        $middlewares = [
            $middlewareAnnotation->getClass(),
        ];

        if (!empty($methodName)) {
            $scanMiddlewares = self::$middlewares[$className]['middlewares']['actions'][$methodName]??[];
            self::$middlewares[$className]['middlewares']['actions'][$methodName] = array_merge($middlewares, $scanMiddlewares);
        } else {
            $scanMiddlewares = self::$middlewares[$className]['middlewares']['group']??[];
            self::$middlewares[$className]['middlewares']['group'] = array_merge($middlewares, $scanMiddlewares);
        }
    }
}