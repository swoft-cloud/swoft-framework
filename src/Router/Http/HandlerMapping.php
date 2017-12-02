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
    /** @var int */
    private $routeCounter = 0;
    private $cacheCounter = 0;

    /** @var string */
    private $currentGroupPrefix;

    /** @var array */
    private $currentGroupOption;

    /** @var bool */
    private $initialized = false;

    /**
     * static Routes - no dynamic argument match
     * 整个路由 path 都是静态字符串 e.g. '/user/login'
     * @var array[]
     * [
     *     '/user/login' => [
     *         // METHODS => [...] // 这里 key 和 value里的 'methods' 是一样的。仅是为了防止重复添加
     *         'GET,POST' => [
     *              'handler' => 'handler',
     *              'methods' => 'GET,POST',
     *              'option' => [...],
     *          ],
     *          'PUT' => [
     *              'handler' => 'handler',
     *              'methods' => 'PUT',
     *              'option' => [...],
     *          ],
     *          ...
     *      ]
     * ]
     */
    private $staticRoutes = [];

    /**
     * regular Routes - have dynamic arguments, but the first node is normal string.
     * 第一节是个静态字符串，称之为有规律的动态路由。按第一节的信息进行分组存储
     * e.g '/hello[/{name}]' '/user/{id}'
     * @var array[]
     * [
     *     // 使用完整的第一节作为key进行分组
     *     'a' => [
     *          [
     *              'start' => '/a/',
     *              'regex' => '/a/(\w+)',
     *              'methods' => 'GET,POST',
     *              'handler' => 'handler',
     *              'option' => [...],
     *          ],
     *          ... ...
     *      ],
     *     'add' => [
     *          [
     *              'start' => '/add/',
     *              'regex' => '/add/(\w+)',
     *              'methods' => 'GET',
     *              'handler' => 'handler',
     *              'option' => [...],
     *          ],
     *          ... ...
     *      ],
     *     'blog' => [
     *        [
     *              'start' => '/blog/post-',
     *              'regex' => '/blog/post-(\w+)',
     *              'methods' => 'GET',
     *              'handler' => 'handler',
     *              'option' => [...],
     *        ],
     *        ... ...
     *     ],
     * ]
     */
    private $regularRoutes = [];

    /**
     * vague Routes - have dynamic arguments,but the first node is exists regex.
     * 第一节就包含了正则匹配，称之为无规律/模糊的动态路由
     * e.g '/{name}/profile' '/{some}/{some2}'
     * @var array
     * [
     *     [
     *         // 必定包含的字符串
     *         'include' => '/profile',
     *         'regex' => '/(\w+)/profile',
     *         'methods' => 'GET',
     *         'handler' => 'handler',
     *         'option' => [...],
     *     ],
     *     [
     *         'include' => null,
     *         'regex' => '/(\w+)/(\w+)',
     *         'methods' => 'GET,POST',
     *         'handler' => 'handler',
     *         'option' => [...],
     *     ],
     *      ... ...
     * ]
     */
    private $vagueRoutes = [];

    /**
     * There are last route caches
     * @see $staticRoutes
     * @var array[]
     */
    private $routeCaches = [];

    /*******************************************************************************
     * route config
     ******************************************************************************/

    /** @var bool 是否忽略最后的URl斜线 '/'. */
    public $ignoreLastSep = false;

    /** @var int 动态路由缓存数 */
    public $tmpCacheNumber = 0;

    /**
     * 配置此项可用于拦截所有请求。 （例如网站维护时）
     *  1. 是个URI字符串， 直接用于解析路由
     *  2. 是个闭包回调，直接调用
     * eg: '/site/maintenance' OR `function () { '系统维护中 :)'; }`
     *
     * @var mixed
     */
    public $matchAll;

    /** @var bool 自动匹配路由(像yii框架)。 如果为True，将自动查找控制器文件。 */
    public $autoRoute = false;

    /** @var string 默认的控制器命名空间, 当开启自动匹配路由时有效. eg: 'App\\Controllers' */
    public $controllerNamespace = '';

    /** @var string 控制器后缀, 当开启自动匹配路由时有效. eg: 'Controller' */
    public $controllerSuffix = 'Controller';

    /**
     * action prefix
     *
     * @var string
     */
    public $actionPrefix = 'action';

    /**
     * default action
     *
     * @var string
     */
    public $defaultAction = 'index';

    /**
     * object creator.
     * @param array $config
     * @return self
     * @throws \LogicException
     */
    public static function make(array $config = [])
    {
        return new static($config);
    }

    /**
     * object constructor.
     * @param array $config
     * @throws \LogicException
     */
    public function __construct(array $config = [])
    {
        $this->setConfig($config);

        $this->currentGroupPrefix = '';
        $this->currentGroupOption = [];
    }

    /**
     * @param array $config
     * @throws \LogicException
     */
    public function setConfig(array $config)
    {
        if ($this->initialized) {
            throw new \LogicException('Routing has been added, and configuration is not allowed!');
        }

        foreach ($config as $name => $value) {
            if (property_exists($this, $name)) {
                $this->$name = $value;
            }
        }
    }

    /*******************************************************************************
     * route collection
     ******************************************************************************/

    /**
     * Defines a route callback and method
     * @param string $method
     * @param array $args
     * @return $this
     * @throws \LogicException
     * @throws \InvalidArgumentException
     */
    public function __call($method, array $args)
    {
        if (\count($args) < 2) {
            throw new \InvalidArgumentException("The method [$method] parameters is missing.");
        }

        return $this->map($method, $args[0], $args[1], $args[2] ?? []);
    }

    /**
     * Create a route group with a common prefix.
     * All routes created in the passed callback will have the given group prefix prepended.
     * @ref package 'nikic/fast-route'
     * @param string $prefix
     * @param \Closure $callback
     * @param array $opts
     */
    public function group($prefix, \Closure $callback, array $opts = [])
    {
        $previousGroupPrefix = $this->currentGroupPrefix;
        $this->currentGroupPrefix = $previousGroupPrefix . '/' . trim($prefix, '/');

        $previousGroupOption = $this->currentGroupOption;
        $this->currentGroupOption = $opts;

        $callback($this);

        $this->currentGroupPrefix = $previousGroupPrefix;
        $this->currentGroupOption = $previousGroupOption;
    }

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
        // validate and format arguments
        $methods = static::validateArguments($methods, $handler);

        if ($route = trim($route)) {
            // always add '/' prefix.
            $route = $route{0} === '/' ? $route : '/' . $route;
        } elseif (!$hasPrefix) {
            $route = '/';
        }

        $route = $this->currentGroupPrefix . $route;

        // setting 'ignoreLastSep'
        if ($route !== '/' && $this->ignoreLastSep) {
            $route = rtrim($route, '/');
        }

        $this->routeCounter++;
        $opts = array_replace([
            'params' => null,
            // 'domains' => null,
        ], $this->currentGroupOption, $opts);
        $conf = [
            'methods' => $methods,
            'handler' => $handler,
            'option' => $opts,
        ];

        // no dynamic param params
        if (self::isNoDynamicParam($route)) {
            $this->staticRoutes[$route][$methods] = $conf;

            return $this;
        }

        // have dynamic param params

        // replace param name To pattern regex
        $params = static::getAvailableParams(self::$globalParams, $opts['params']);
        list($first, $conf) = static::parseParamRoute($route, $params, $conf);

        // route string have regular
        if ($first) {
            $this->regularRoutes[$first][] = $conf;
        } else {
            $this->vagueRoutes[] = $conf;
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
    public function match($path, $method = self::GET)
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

        $path = self::formatUriPath($path, $this->ignoreLastSep);
        $method = strtoupper($method);

        // find in route caches.
        if ($this->routeCaches && isset($this->routeCaches[$path])) {
            $data = self::findInStaticRoutes($this->routeCaches[$path], $path, $method);

            if ($data[0] === self::FOUND) {
                return $data;
            }
        }

        // is a static route path
        if ($this->staticRoutes && isset($this->staticRoutes[$path])) {
            return self::findInStaticRoutes($this->staticRoutes[$path], $path, $method);
        }

        $first = self::getFirstFromPath($path);
        $founded = [];

        // is a regular dynamic route(the first node is 1th level index key).
        if (isset($this->regularRoutes[$first])) {
            foreach ($this->regularRoutes[$first] as $conf) {
                if (0 === strpos($path, $conf['start']) && preg_match($conf['regex'], $path, $matches)) {
                    $conf['matches'] = $matches;
                    $founded[] = $conf;
                }
            }

            if ($founded) {
                return $this->findInPossibleParamRoutes($founded, $path, $method);
            }
        }

        // is a irregular dynamic route
        foreach ($this->vagueRoutes as $conf) {
            if ($conf['include'] && false === strpos($path, $conf['include'])) {
                continue;
            }

            if (preg_match($conf['regex'], $path, $matches)) {
                $conf['matches'] = $matches;
                $founded[] = $conf;
            }
        }

        if ($founded) {
            return $this->findInPossibleParamRoutes($founded, $path, $method);
        }

        // handle Auto Route
        if (
            $this->autoRoute &&
            ($handler = self::matchAutoRoute($path, $this->controllerNamespace, $this->controllerSuffix))
        ) {
            return [self::FOUND, $path, [
                'handler' => $handler,
                'option' => [],
            ]];
        }

        // oo ... not found
        return [self::NOT_FOUND, $path, null];
    }

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

    /*******************************************************************************
     * helper methods
     ******************************************************************************/

    /**
     * @param array $routes
     * @param string $path
     * @param string $method
     * @return array
     */
    protected function findInPossibleParamRoutes(array $routes, $path, $method)
    {
        $methods = null;

        foreach ($routes as $conf) {
            if (false !== strpos($conf['methods'] . ',', $method . ',')) {
                $conf['matches'] = self::filterMatches($conf['matches'], $conf);

                $this->cacheMatchedParamRoute($path, $conf);

                return [self::FOUND, $path, $conf];
            }

            $methods .= $conf['methods'] . ',';
        }

        // method not allowed
        return [
            self::METHOD_NOT_ALLOWED,
            $path,
            array_unique(explode(',', trim($methods, ',')))
        ];
    }

    /**
     * @param string $path
     * @param array $conf
     */
    protected function cacheMatchedParamRoute($path, array $conf)
    {
        $methods = $conf['methods'];
        $cacheNumber = (int)$this->tmpCacheNumber;

        // cache last $cacheNumber routes.
        if ($cacheNumber > 0) {
            if ($this->cacheCounter === $cacheNumber) {
                array_shift($this->routeCaches);
            }

            if (!isset($this->routeCaches[$path][$methods])) {
                $this->cacheCounter++;
                $this->routeCaches[$path][$methods] = $conf;
            }
        }
    }

    /**
     * @return int
     */
    public function count()
    {
        return $this->routeCounter;
    }

    /**
     * @param array $staticRoutes
     */
    public function setStaticRoutes(array $staticRoutes)
    {
        $this->staticRoutes = $staticRoutes;
    }

    /**
     * @return array
     */
    public function getStaticRoutes()
    {
        return $this->staticRoutes;
    }

    /**
     * @param \array[] $regularRoutes
     */
    public function setRegularRoutes(array $regularRoutes)
    {
        $this->regularRoutes = $regularRoutes;
    }

    /**
     * @return \array[]
     */
    public function getRegularRoutes()
    {
        return $this->regularRoutes;
    }

    /**
     * @param array $vagueRoutes
     */
    public function setVagueRoutes($vagueRoutes)
    {
        $this->vagueRoutes = $vagueRoutes;
    }

    /**
     * @return array
     */
    public function getVagueRoutes()
    {
        return $this->vagueRoutes;
    }

    /**
     * @return array
     */
    public function getRouteCaches()
    {
        return $this->routeCaches;
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
            $actionMethod = $this->getActionMethod($this->actionPrefix, $action);
            $mapRoute     = empty($mapRoute) ? $actionMethod : $mapRoute;

            // '/'开头的路由是一个单独的路由，未使用'/'需要和控制器组拼成一个路由
            $uri     = strpos($mapRoute, '/') === 0 ? $mapRoute : $controllerPrefix . '/' . $mapRoute;
            $handler = $className . '@' . $action;

            // 注入路由规则
            $this->map($method, $uri, $handler, []);
        }
    }

    /**
     * 获取action方法
     *
     * @param string $actionPrefix 配置的默认action前缀
     * @param string $action       action方法
     *
     * @return string
     */
    private function getActionMethod(string $actionPrefix, string $action)
    {
        $prefixes = [$actionPrefix, ucfirst($actionPrefix)];
        $action = str_replace($prefixes, '', $action);
        $action = lcfirst($action);

        return $action;
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
