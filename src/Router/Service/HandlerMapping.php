<?php

namespace Swoft\Router\Service;

use Swoft\Router\HandlerMappingInterface;

/**
 * handler of service
 *
 * @uses      HandlerMapping
 * @version   2017年11月23日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class HandlerMapping implements HandlerMappingInterface
{

    /**
     * service service
     *
     * @var string
     */
    private $suffix = 'Service';

    /**
     * service routes
     *
     * @var array
     */
    private $routes = [];

    /**
     * get handler from router
     *
     * @param array ...$params
     *
     * @return array
     */
    public function getHandler(...$params)
    {
        list($data) = $params;
        $func = $data['func']?? '';

        return $this->match($func);
    }

    /**
     * auto register routes
     *
     * @param array $serviceMapping
     */
    public function register(array $serviceMapping)
    {
        foreach ($serviceMapping as $className => $mapping) {
            $prefix = $mapping['name'];
            $routes = $mapping['routes'];
            $prefix = $this->getPrefix($this->suffix, $prefix, $className);

            $this->registerRoute($className, $routes, $prefix);
        }
    }

    /**
     * match route
     *
     * @param $func
     *
     * @return mixed
     */
    public function match($func)
    {
        if (!isset($this->routes[$func])) {
            throw new \InvalidArgumentException('the func of service is not exist，func=' . $func);
        }

        return $this->routes[$func];
    }

    /**
     * register one route
     *
     * @param string $className
     * @param array  $routes
     * @param string $prefix
     */
    private function registerRoute(string $className, array $routes, string $prefix)
    {
        foreach ($routes as $route) {
            $mappedName = $route['mappedName'];
            $methodName = $route['methodName'];
            if (empty($mappedName)) {
                $mappedName = $methodName;
            }

            $serviceKey                = "$prefix::$mappedName";
            $this->routes[$serviceKey] = [$className, $methodName];
        }
    }

    /**
     * get service from class name
     *
     * @param string $suffix
     * @param string $prefix
     * @param string $className
     *
     * @return string
     */
    private function getPrefix(string $suffix, string $prefix, string $className)
    {
        // the  prefix of annotation is exist
        if (!empty($prefix)) {
            return $prefix;
        }

        // the prefix of annotation is empty
        $reg    = '/^.*\\\(\w+)' . $suffix . '$/';
        $prefix = '';

        if ($result = preg_match($reg, $className, $match)) {
            $prefix = ucfirst($match[1]);
        }

        return $prefix;
    }
}
