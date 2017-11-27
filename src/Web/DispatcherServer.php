<?php

namespace Swoft\Web;

use Swoft\App;
use Swoft\Base\DispatcherInterface;
use Swoft\Base\RequestContext;
use Swoft\Base\RequestHandler;
use Swoft\Bean\Annotation\Bean;
use Swoft\Event\Event;
use Swoft\Exception\Handler\ExceptionHandlerManager;
use Swoft\Middleware\Http\FaviconIcoMiddleware;
use Swoft\Middleware\Http\HandlerAdapterMiddleware;
use Swoft\Middleware\Http\PoweredByMiddleware;
use Swoft\Middleware\Http\RouterMiddleware;

/**
 * the dispatcher of http server
 *
 * @Bean("dispatcherServer")
 * @uses      DispatcherServer
 * @version   2017年11月24日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class DispatcherServer implements DispatcherInterface
{
    /**
     * user defined middlewares
     *
     * @var array
     */
    private $middlewares
        = [

        ];

    /**
     * handler adapter
     *
     * @var string
     */
    private $handlerAdapter = HandlerAdapterMiddleware::class;

    /**
     * do dispatcher
     *
     * @param array ...$params
     *
     * @return bool|\Swoft\Web\Response
     */
    public function doDispatcher(...$params)
    {
        /**
         * @var \Swoole\Http\Request  $swooleRequest
         * @var \Swoole\Http\Response $swooleResponse
         */
        list($swooleRequest, $swooleResponse) = $params;

        try {
            // before dispatcher
            $this->beforeDispatcher($swooleRequest, $swooleResponse);

            // request middlewares
            $middlewares    = $this->requestMiddlewares();
            $request        = RequestContext::getRequest();
            $requestHandler = new RequestHandler($middlewares, $this->handlerAdapter);
            $response       = $requestHandler->handle($request);
        } catch (\Throwable $throwable) {
//            var_dump($throwable->getMessage(), $throwable->getFile(), $throwable->getLine(), $throwable->getCode());
            // Handle by ExceptionHandler
            $response = ExceptionHandlerManager::handle($throwable);
        } finally {
            $this->afterDispatcher($response);
        }

        return $response;
    }

    public function requestMiddlewares()
    {
        $annotationMiddelwares = [];

        return array_merge($this->firstMiddlewares(), $this->middlewares, $annotationMiddelwares, $this->lastMiddlewares());
    }

    /**
     * the firsted middlewares of request
     *
     * @return array
     */
    public function firstMiddlewares()
    {
        return [
            FaviconIcoMiddleware::class,
            PoweredByMiddleware::class,
            RouterMiddleware::class,
        ];
    }

    /**
     * the lasted middlewares of request
     *
     * @return array
     */
    public function lastMiddlewares()
    {
        return [

        ];
    }

    /**
     * before dispatcher
     *
     * @param \Swoole\Http\Request  $request  swoole request
     * @param \Swoole\Http\Response $response swoole response
     */
    protected function beforeDispatcher(\Swoole\Http\Request $request, \Swoole\Http\Response $response)
    {
        // Initialize Request and Response and set to RequestContent
        RequestContext::setRequest($request);
        RequestContext::setResponse($response);

        // Trigger 'Before Request' event
        App::trigger(Event::BEFORE_REQUEST);
    }

    /**
     * If $response is not an instance of Response,
     * usually return by Action of Controller,
     * then the auto() method will format the result
     * and return a suitable response
     *
     * @param mixed $response
     */
    protected function afterDispatcher($response)
    {
        if (!$response instanceof Response) {
            $response = RequestContext::getResponse()->auto($response);
        }

        // Handle Response
        $response->send();

        // Trigger 'After Request' event
        App::trigger(Event::AFTER_REQUEST);
    }
}