<?php

namespace Swoft\Middleware\Http;

use Interop\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Swoft\App;
use Swoft\Bean\Annotation\Bean;
use Swoft\Middleware\MiddlewareInterface;

/**
 * the middleware of request parsers
 *
 * @Bean()
 * @uses      ParserMiddleware
 * @version   2017年12月02日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class ParserMiddleware implements MiddlewareInterface
{

    /**
     * do process
     *
     * @param \Psr\Http\Message\ServerRequestInterface     $request
     * @param \Interop\Http\Server\RequestHandlerInterface $handler
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        /* @var \Swoft\Web\RequestParser $requestParser */
        $requestParser = App::getBean('requestParser');
        $request       = $requestParser->parser($request);

        return $handler->handle($request);
    }
}
