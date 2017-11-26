<?php

namespace Swoft\Middleware\Service;

use Interop\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Swoft\App;
use Swoft\Bean\Annotation\Bean;
use Swoft\Middleware\MiddlewareInterface;

/**
 * service router
 *
 * @Bean()
 * @uses      RouterMiddleware
 * @version   2017年11月26日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class RouterMiddleware implements MiddlewareInterface
{
    /**
     * the attributed key of service
     */
    const ATTRIBUTE = "serviceHandler";

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // service data
        $data = $request->getAttribute(PackerMiddleware::ATTRIBUTE_DATA);

        /* @var \Swoft\Router\Service\HandlerMapping $serviceRouter*/
        $serviceRouter = App::getBean('serviceRouter');
        $serviceHandler = $serviceRouter->getHandler($data);

        // deliver service data
        $request = $request->withAttribute(self::ATTRIBUTE, $serviceHandler);
        return $handler->handle($request);
    }
}