<?php

namespace Swoft\Middleware\Http;

use Interop\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Swoft\App;
use Swoft\Bean\Annotation\Bean;
use Swoft\Middleware\MiddlewareInterface;

/**
 * route middleware
 *
 * @Bean()
 * @uses      RouterMiddleware
 * @version   2017年11月25日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class RouterMiddleware implements MiddlewareInterface
{
    /**
     * the attributed key of request
     */
    const ATTRIBUTE = "requestHandler";

    /**
     * request router
     *
     * @param \Psr\Http\Message\ServerRequestInterface     $request
     * @param \Interop\Http\Server\RequestHandlerInterface $handler
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $path        = $request->getUri()->getPath();
        $method      = $request->getMethod();
        $httpHandler = App::getHttpRouter()->getHandler($path, $method);
        $request     = $request->withAttribute(self::ATTRIBUTE, $httpHandler);

        return $handler->handle($request);
    }
}
