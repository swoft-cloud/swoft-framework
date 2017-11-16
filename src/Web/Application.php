<?php

namespace Swoft\Web;

use Psr\Http\Message\ResponseInterface;
use Swoft\App;
use Swoft\Base\RequestContext;
use Swoft\Event\Event;
use Swoft\Exception\Http\RouteNotFoundException;
use Swoft\Filter\FilterChain;
use Swoft\Helper\ResponseHelper;
use Swoft\Web\ExceptionHandler\ExceptionHandlerManager;
use Swoft\Web\Middlewares;
use Swoft\Web\Middlewares\PowerByMiddlewre;

/**
 * 应用主体
 *
 * @uses      Application
 * @version   2017年04月25日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 Swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class Application extends \Swoft\Base\Application
{

    /**
     * Define the middlewares stack
     *
     * @var array
     */
    protected $middlewares
        = [
            Middlewares\FaviconIco::class,
            Middlewares\PoweredBy::class,
        ];

    /**
     * handle request
     *
     * @param \Swoole\Http\Request $request Swoole request object
     * @param \Swoole\Http\Response $response Swoole response object
     * @return bool|\Swoft\Base\Response
     */
    public function doRequest(\Swoole\Http\Request $request, \Swoole\Http\Response $response)
    {
        try {
            // Initialize Request and Response and set to RequestContent
            RequestContext::setRequest($request);
            RequestContext::setResponse($response);

            // Trigger 'Before Request' event
            App::trigger(Event::BEFORE_REQUEST);

            $actionResponse = $this->dispatch(RequestContext::getRequest());

        } catch (\Throwable $t) {
            // Handle by ExceptionHandler
            $actionResponse = ExceptionHandlerManager::handle($t);
        } finally {
            if (! $actionResponse instanceof Response) {
                /**
                 * If $response is not an instance of Response,
                 * usually return by Action of Controller,
                 * then the auto() method will format the result
                 * and return a suitable response
                 */
                $actionResponse = RequestContext::getResponse()->auto($actionResponse);
            }
            // Handle Response
            $actionResponse->send();

            // Trigger 'After Request' event
            App::trigger(Event::AFTER_REQUEST);
        }
        //
        return $actionResponse;
    }

    /**
     * rpc内部服务
     *
     * @param \Swoole\Server $server
     * @param int $fd
     * @param int $from_id
     * @param string $data
     */
    public function doReceive(\Swoole\Server $server, int $fd, int $from_id, string $data)
    {
        try {
            // 解包
            $packer = App::getPacker();
            $data = $packer->unpack($data);

            App::trigger(Event::BEFORE_RECEIVE, null, $data);

            // 执行函数调用
            $response = $this->runService($data);
            $data = $packer->pack($response);
        } catch (\Exception $e) {
            $code = $e->getCode();
            $message = $e->getMessage();
            $data = ResponseHelper::formatData("", $code, $message);
        }

        App::trigger(Event::AFTER_REQUEST);
        $server->send($fd, $data);
    }

    /**
     * 运行控制器
     *
     * @param string $uri
     * @param string $method
     * @return \Swoft\Web\Response
     * @throws \Exception
     */
    public function runController(string $uri, string $method = "get")
    {
        /* @var Router $router */
        $router = App::getBean('router');

        // 路由解析
        App::profileStart("router.match");
        list($path, $info) = $router->match($uri, $method);
        App::profileEnd("router.match");

        // 路由未定义处理
        if ($info == null) {
            throw new RouteNotFoundException("Route not found");
        }

        /* @var Controller $controller */
        list($controller, $actionId, $params) = $this->createController($path, $info);

        /* run controller with Filters */
        return $this->runControllerWithFilters($controller, $actionId, $params);
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
     * @param Request $request
     * @return array
     */
    protected function route(Request $request): array
    {
        /* @var Router $router */
        $router = App::getBean('router');

        $uri = $request->getUri()->getPath();
        $method = $request->getMethod();

        App::profileStart("router.match");

        $route = $router->match($uri, $method);

        RequestContext::setContextDataByKey('route', $route);
        App::profileEnd("router.match");
        return $route;
    }

    /**
     * @param Request $request
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function dispatch(Request $request): ResponseInterface
    {
        list($path, $info) = $this->route($request);

        // TODO: Add by @Middleware()
        $userMiddlewares = [];

        $middlewares = array_merge($this->middlewares, $userMiddlewares);

        // Dispatch request through middlewares and terminators,
        // if throw an exception in process will stop the
        $dispatcher = new Dispatcher($middlewares);
        $actionResponse = $dispatcher->dispatch($request);
        return $actionResponse;
    }

}
