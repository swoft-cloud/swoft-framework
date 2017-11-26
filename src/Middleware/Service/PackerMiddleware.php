<?php

namespace Swoft\Middleware\Service;

use Interop\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Swoft\App;
use Swoft\Bean\Annotation\Bean;
use Swoft\Event\Event;
use Swoft\Middleware\MiddlewareInterface;
use Swoft\Router\Service\HandlerAdapter;

/**
 * service packer
 *
 * @Bean()
 * @uses      PackerMiddleware
 * @version   2017年11月26日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class PackerMiddleware implements MiddlewareInterface
{
    /**
     * the server param of service
     */
    const ATTRIBUTE_SERVER = 'serviceRequestServer';

    /**
     * the fd param of service
     */
    const ATTRIBUTE_FD = 'serviceRequestFd';

    /**
     * the fromid param of service
     */
    const ATTRIBUTE_FROMID = 'serviceRequestFromid';

    /**
     * the data param of service
     */
    const ATTRIBUTE_DATA = 'serviceRequestData';

    /**
     * packer middleware
     *
     * @param \Psr\Http\Message\ServerRequestInterface     $request
     * @param \Interop\Http\Server\RequestHandlerInterface $handler
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        /* @var \Swoft\Service\DispatcherService $dispatcherService */
        $dispatcherService = App::getBean('dispatcherService');
        $packer            = $dispatcherService->getPacker();
        $data              = $request->getAttribute(self::ATTRIBUTE_DATA);
        $data              = $packer->unpack($data);
        App::trigger(Event::BEFORE_RECEIVE, null, $data);
        $request = $request->withAttribute(self::ATTRIBUTE_DATA, $data);

        /* @var \Swoft\Base\Response $response */
        $response      = $handler->handle($request);
        $serviceResult = $response->getAttribute(HandlerAdapter::ATTRIBUTE);
        $serviceResult = $packer->pack($serviceResult);

        return $response->withAttribute(HandlerAdapter::ATTRIBUTE, $serviceResult);
    }
}