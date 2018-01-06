<?php

namespace Swoft\Middleware\Http;

use Interop\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Swoft\Core\RequestHandler;
use Swoft\Bean\Annotation\Bean;
use Swoft\Bean\Collector;
use Swoft\Middleware\MiddlewareInterface;

/**
 * the annotation middlewares of action
 *
 * @Bean()
 * @uses      UserMiddleware
 * @version   2017年11月28日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class UserMiddleware implements MiddlewareInterface
{
    /**
     * do middlewares of action
     *
     * @param \Psr\Http\Message\ServerRequestInterface     $request
     * @param \Interop\Http\Server\RequestHandlerInterface $handler
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $httpHandler = $request->getAttribute(RouterMiddleware::ATTRIBUTE);
        $info = $httpHandler[2];

        $actionMiddlewares = [];
        if (isset($info['handler']) && is_string($info['handler'])) {
            // Extract action info from router handler
            $exploded             = explode('@', $info['handler']);
            $controllerClass      = $exploded[0] ?? '';
            $action               = isset($exploded[1]) ? $exploded[1] : '';
            $collectedMiddlewares = Collector::$requestMapping[$controllerClass]['middlewares']??[];

            // Get group middleware from Collector
            if ($controllerClass) {
                $collect = $collectedMiddlewares['group'] ?? [];
                $collect && $actionMiddlewares = array_merge($actionMiddlewares, $collect);
            }
            // Get the specified action middleware from Collector
            if ($action) {
                $collect = $collectedMiddlewares['actions'][$action]??[];
                $collect && $actionMiddlewares = array_merge($actionMiddlewares, $collect ?? []);
            }
        }
        if (!empty($actionMiddlewares) && $handler instanceof RequestHandler) {
            $handler->insertMiddlewares($actionMiddlewares);
        }

        return $handler->handle($request);
    }
}
