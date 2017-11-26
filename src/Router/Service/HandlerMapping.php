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
     * 匹配路由
     *
     * @param $func
     *
     * @return mixed
     */
    public function match($func)
    {
        if (!isset($this->routes[$func])) {
            throw new \InvalidArgumentException('service调用的函数不存在，func=' . $func);
        }

        return $this->routes[$func];
    }

    /**
     * 注册一个路由
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
     * 获取类前缀
     *
     * @param string $suffix
     * @param string $prefix
     * @param string $className
     *
     * @return string
     */
    private function getPrefix(string $suffix, string $prefix, string $className)
    {
        // 注解注入不为空，直接返回prefix
        if (!empty($prefix)) {
            return $prefix;
        }

        // 注解注入为空，解析控制器prefix
        $reg    = '/^.*\\\(\w+)' . $suffix . '$/';
        $prefix = '';

        if ($result = preg_match($reg, $className, $match)) {
            $prefix = ucfirst($match[1]);
        }

        return $prefix;
    }
}