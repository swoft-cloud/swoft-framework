<?php

namespace Swoft\Router\Http;

use Swoft\Router\HandlerMappingInterface;

/**
 * handler mapping of http
 *
 * @uses      HandlerMapping
 * @version   2017年07月14日
 * @author    inhere <in.798@qq.com>
 * @copyright Copyright 2010-2016 Swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 *
 * @method get(string $route, mixed $handler, array $opts = [])
 * @method post(string $route, mixed $handler, array $opts = [])
 * @method put(string $route, mixed $handler, array $opts = [])
 * @method delete(string $route, mixed $handler, array $opts = [])
 * @method options(string $route, mixed $handler, array $opts = [])
 * @method head(string $route, mixed $handler, array $opts = [])
 * @method search(string $route, mixed $handler, array $opts = [])
 * @method trace(string $route, mixed $handler, array $opts = [])
 * @method any(string $route, mixed $handler, array $opts = [])
 */
class HandlerMapping extends AbstractRouter implements HandlerMappingInterface
{
    /**
     * default action
     *
     * @var string
     */
    public $defaultAction = 'index';

    /** @var int */
    private $routeCounter = 0;
    private $cacheCounter = 0;

    /*******************************************************************************
     * route collection
     ******************************************************************************/

    /**
     * @param string|array $methods The match request method(s).
     * e.g
     *  string: 'get'
     *  array: ['get','post']
     * @param string $route The route path string. is allow empty string. eg: '/user/login'
     * @param callable|string $handler
     * @param array $opts some option data
     * [
     *     'params' => [ 'id' => '[0-9]+', ],
     *     'defaults' => [ 'id' => 10, ],
     *     'domains'  => [ 'a-domain.com', '*.b-domain.com'],
     *     'schemas' => ['https'],
     * ]
     * @return static
     * @throws \LogicException
     * @throws \InvalidArgumentException
     */
    public function map($methods, $route, $handler, array $opts = [])
    {
        if (!$this->initialized) {
            $this->initialized = true;
        }

        $hasPrefix = (bool)$this->currentGroupPrefix;
        $methods = static::validateArguments($methods, $handler);

        // always add '/' prefix.
        if ($route = trim($route)) {
            $route = $route{0} === '/' ? $route : '/' . $route;
        } elseif (!$hasPrefix) {
            $route = '/';
        }

        $route = $this->currentGroupPrefix . $route;

        // setting 'ignoreLastSlash'
        if ($route !== '/' && $this->ignoreLastSlash) {
            $route = rtrim($route, '/');
        }

        $opts = array_merge($this->currentGroupOption, $opts);
        $conf = [
            'handler' => $handler,
            'option' => $opts,
        ];

        // it is static route
        if (self::isStaticRoute($route)) {
            foreach (explode(',', $methods) as $method) {
                $this->routeCounter++;
                $this->staticRoutes[$route][$method] = $conf;
            }

            return $this;
        }

        $params = $this->getAvailableParams($opts['params'] ?? []);
        list($first, $conf) = $this->parseParamRoute($route, $params, $conf);

        // route string have regular
        if ($first) {
            $this->routeCounter++;
            $conf['methods'] = $methods;
            $this->regularRoutes[$first][] = $conf;
        } else {
            foreach (explode(',', $methods) as $method) {
                $this->routeCounter++;
                $this->vagueRoutes[$method][] = $conf;
            }
        }

        return $this;
    }

    /*******************************************************************************
     * route match
     ******************************************************************************/

    /**
     * find the matched route info for the given request uri path
     * @param string $method
     * @param string $path
     * @return array
     */
    public function match($path, $method = 'GET')
    {
        // if enable 'matchAll'
        if ($matchAll = $this->matchAll) {
            if (\is_string($matchAll) && $matchAll{0} === '/') {
                $path = $matchAll;
            } elseif (\is_callable($matchAll)) {
                return [self::FOUND, $path, [
                    'handler' => $matchAll,
                    'option' => [],
                ]];
            }
        }

        $path = $this->formatUriPath($path, $this->ignoreLastSlash);
        $method = strtoupper($method);

        // find in route caches.
        if ($this->routeCaches && isset($this->routeCaches[$path][$method])) {
            return [self::FOUND, $path, $this->routeCaches[$path][$method]];
        }

        // is a static route path
        if ($this->staticRoutes && isset($this->staticRoutes[$path][$method])) {
            $conf = $this->staticRoutes[$path][$method];

            return [self::FOUND, $path, $conf];
        }

        $first = $this->getFirstFromPath($path);
        // $nodeCount = substr_count(trim($path), '/');
        $allowedMethods = [];

        // is a regular dynamic route(the first node is 1th level index key).
        if (isset($this->regularRoutes[$first])) {
            $result = $this->findInRegularRoutes($this->regularRoutes[$first], $path, $method);

            if ($result[0] === self::FOUND) {
                return $result;
            }

            $allowedMethods = $result[1];
        }

        // is a irregular dynamic route
        if (isset($this->vagueRoutes[$method])) {
            $result = $this->findInVagueRoutes($this->vagueRoutes[$method], $path, $method);

            if ($result[0] === self::FOUND) {
                return $result;
            }
        }

        // handle Auto Route
        if ($this->autoRoute && ($handler = $this->matchAutoRoute($path))) {
            return [self::FOUND, $path, [
                'handler' => $handler,
                'option' => [],
            ]];
        }

        // For HEAD requests, attempt fallback to GET
        if ($method === self::HEAD) {
            if (isset($this->routeCaches[$path]['GET'])) {
                return [self::FOUND, $path, $this->routeCaches[$path]['GET']];
            }

            if (isset($this->staticRoutes[$path]['GET'])) {
                return [self::FOUND, $path, $this->staticRoutes[$path]['GET']];
            }

            if (isset($this->regularRoutes[$first])) {
                $result = $this->findInRegularRoutes($this->regularRoutes[$first], $path, 'GET');

                if ($result[0] === self::FOUND) {
                    return $result;
                }
            }

            if (isset($this->vagueRoutes['GET'])) {
                $result = $this->findInVagueRoutes($this->vagueRoutes['GET'], $path, 'GET');

                if ($result[0] === self::FOUND) {
                    return $result;
                }
            }
        }

        // If nothing else matches, try fallback routes. $router->any('*', 'handler');
        if ($this->staticRoutes && isset($this->staticRoutes['/*'][$method])) {
            return [self::FOUND, $path, $this->staticRoutes['/*'][$method]];
        }

        if ($this->notAllowedAsNotFound) {
            return [self::NOT_FOUND, $path, null];
        }

        // collect allowed methods from: staticRoutes, vagueRoutes
        if (isset($this->staticRoutes[$path])) {
            $allowedMethods = array_merge($allowedMethods, array_keys($this->staticRoutes[$path]));
        }

        foreach ($this->vagueRoutes as $m => $routes) {
            if ($method === $m) {
                continue;
            }

            $result = $this->findInVagueRoutes($this->vagueRoutes['GET'], $path, $m);

            if ($result[0] === self::FOUND) {
                $allowedMethods[] = $method;
            }
        }

        if ($allowedMethods && ($list = array_unique($allowedMethods))) {
            return [self::METHOD_NOT_ALLOWED, $path, $list];
        }

        // oo ... not found
        return [self::NOT_FOUND, $path, null];
    }

    /*******************************************************************************
     * helper methods
     ******************************************************************************/

    /**
     * @param array $routesData
     * @param string $path
     * @param string $method
     * @return array
     */
    protected function findInRegularRoutes(array $routesData, $path, $method)
    {
        $allowedMethods = '';

        foreach ($routesData as $conf) {
            if (0 === strpos($path, $conf['start']) && preg_match($conf['regex'], $path, $matches)) {
                $allowedMethods .= $conf['methods'] . ',';

                if (false !== strpos($conf['methods'] . ',', $method . ',')) {
                    $conf['matches'] = $this->filterMatches($matches, $conf);

                    $this->cacheMatchedParamRoute($path, $method, $conf);

                    return [self::FOUND, $path, $conf];
                }
            }
        }

        return [self::NOT_FOUND, explode(',', trim($allowedMethods, ','))];
    }

    /**
     * @param array $routesData
     * @param string $path
     * @param string $method
     * @return array
     */
    protected function findInVagueRoutes(array $routesData, $path, $method)
    {
        foreach ($routesData as $conf) {
            if ($conf['include'] && false === strpos($path, $conf['include'])) {
                continue;
            }

            if (preg_match($conf['regex'], $path, $matches)) {
                $conf['matches'] = $this->filterMatches($matches, $conf);

                $this->cacheMatchedParamRoute($path, $method, $conf);

                return [self::FOUND, $path, $conf];
            }
        }

        return [self::NOT_FOUND];
    }

    /**
     * @param string $path
     * @param string $method
     * @param array $conf
     */
    protected function cacheMatchedParamRoute($path, $method, array $conf)
    {
        $cacheNumber = (int)$this->tmpCacheNumber;

        // cache last $cacheNumber routes.
        if ($cacheNumber > 0 && !isset($this->routeCaches[$path][$method])) {
            if ($this->cacheCounter >= $cacheNumber) {
                array_shift($this->routeCaches);
            }

            $this->cacheCounter++;
            $this->routeCaches[$path][$method] = $conf;
        }
    }

    /**
     * @return int
     */
    public function count()
    {
        return $this->routeCounter;
    }

    /*******************************************************************************
     * other helper methods
     ******************************************************************************/

    /**
     * get handler from router
     *
     * @param array ...$params
     *
     * @return array
     */
    public function getHandler(...$params)
    {
        list($path, $method) = $params;
        // list($path, $info) = $router;

        return $this->match($path, $method);
    }

    /**
     * 自动注册路由
     *
     * @param array $requestMapping
     */
    public function registerRoutes(array $requestMapping)
    {
        foreach ($requestMapping as $className => $mapping) {
            if (!isset($mapping['prefix'], $mapping['routes'])) {
                continue;
            }

            // 控制器prefix
            $controllerPrefix = $mapping['prefix'];
            $controllerPrefix = $this->getControllerPrefix($controllerPrefix, $className);
            $routes           = $mapping['routes'];
            // 注册控制器对应的一组路由
            $this->registerRoute($className, $routes, $controllerPrefix);
        }
    }

    /**
     * 注册路由
     *
     * @param string $className        类名
     * @param array  $routes           控制器对应的路由组
     * @param string $controllerPrefix 控制器prefix
     */
    private function registerRoute(string $className, array $routes, string $controllerPrefix)
    {
        // 循环注册路由
        foreach ($routes as $route) {
            if (!isset($route['route'], $route['method'], $route['action'])) {
                continue;
            }
            $mapRoute = $route['route'];
            $method   = $route['method'];
            $action   = $route['action'];

            // 解析注入action名称
            $mapRoute = empty($mapRoute) ? $action : $mapRoute;

            // '/'开头的路由是一个单独的路由，未使用'/'需要和控制器组拼成一个路由
            $uri     = strpos($mapRoute, '/') === 0 ? $mapRoute : $controllerPrefix . '/' . $mapRoute;
            $handler = $className . '@' . $action;

            // 注入路由规则
            $this->map($method, $uri, $handler, []);
        }
    }

    /**
     * 获取控制器prefix
     *
     * @param string $controllerPrefix 注解控制器prefix
     * @param string $className        控制器类名
     *
     * @return string
     */
    private function getControllerPrefix(string $controllerPrefix, string $className)
    {
        // 注解注入不为空，直接返回prefix
        if (!empty($controllerPrefix)) {
            return $controllerPrefix;
        }

        // 注解注入为空，解析控制器prefix
        $reg    = '/^.*\\\(\w+)' . $this->controllerSuffix . '$/';
        $prefix = '';

        if ($result = preg_match($reg, $className, $match)) {
            $prefix = '/' . lcfirst($match[1]);
        }

        return $prefix;
    }
}
