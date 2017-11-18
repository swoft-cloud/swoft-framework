<?php

namespace Swoft\Web\Middlewares;

use Interop\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Swoft\App;
use Swoft\Base\ApplicationContext;
use Swoft\Base\RequestContext;
use Swoft\Bean\Annotation\Bean;
use Swoft\Exception\Http\RouteNotFoundException;
use Swoft\Testing\Web\Response;
use Swoft\Web\Controller;
use Swoft\Web\Router;


/**
 * @Bean()
 * @uses      ControllerMiddleware
 * @version   2017年11月16日
 * @author    huangzhhui <huangzhwork@gmail.com>
 * @copyright Copyright 2010-2017 Swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class ControllerMiddleware implements MiddlewareInterface
{

    /**
     * Process an incoming server request and return a response, optionally delegating
     * response creation to a handler.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Interop\Http\Server\RequestHandlerInterface $handler
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {

        $route = RequestContext::getContextDataByKey('route');

        list($path, $info) = $route;

        // Route not found
        if ($info == null) {
            throw new RouteNotFoundException("Route not found");
        }

        list($controller, $actionId, $params) = $this->createController($path, $info);
        // run controller with Filters
        $response = $this->runControllerWithFilters($controller, $actionId, $params);

        if (! $response instanceof Response) {
            /**
             * If $response is not instance of Response,
             * usually return by Action of Controller,
             * then the auto() method will format the result
             * and return a suitable response
             */
            $response = RequestContext::getResponse()->auto($response);
        }
        return $response;
    }

    /**
     * run controller with Filters
     *
     * @param Controller $controller 控制器
     * @param string $actionId actionID
     * @param array $params action参数
     * @return \Swoft\Web\Response
     */
    private function runControllerWithFilters(Controller $controller, string $actionId, array $params)
    {
        $request = App::getRequest();
        $response = App::getResponse();

        /* @var FilterChain $filter */
        $filter = App::getBean('filter');

        App::profileStart("Filter");
        $result = $filter->doFilter($request, $response, $filter);
        App::profileEnd("Filter");

        if ($result) {
            $response = $controller->run($actionId, $params);
            return $response;
        }
    }

    /**
     * 创建控制器
     *
     * @param string $path url路径
     * @param array $info url参数
     * @return array
     * <pre>
     *  [$controller, $action, $matches]
     * </pre>
     * @throws \InvalidArgumentException
     */
    public function createController(string $path, array $info)
    {
        $handler = $info['handler'];
        $matches = $info['matches'] ?? [];

        // Remove $matches[0] as [1] is the first parameter.
        if ($matches) {
            array_shift($matches);
            $matches = array_values($matches);
        }

        // is a \Closure or a callable object
        if (is_object($handler)) {
            return $matches ? $handler(...$matches) : $handler();
        }

        //// $handler is string

        // is array ['controller', 'action']
        if (is_array($handler)) {
            $segments = $handler;
        } elseif (is_string($handler)) {
            // e.g `Controllers\Home@index` Or only `Controllers\Home`
            $segments = explode('@', trim($handler));
        } else {
            App::error('Invalid route handler for URI: ' . $path);
            throw new \InvalidArgumentException('Invalid route handler for URI: ' . $path);
        }

        $action = '';
        $className = $segments[0];

        if (isset($segments[1])) {
            // Already assign action
            $action = $segments[1];
        } elseif (isset($matches[0])) {
            // use dynamic action
            $action = array_shift($matches);
        }

        $action = Router::convertNodeStr($action);
        $controller = ApplicationContext::getBean($className);
        // Set Controller and Action infos to Request Context
        RequestContext::setContextData([
            'controllerClass' => $className,
            'controllerAction' => $action,
        ]);

        return [$controller, $action, $matches];
    }
}