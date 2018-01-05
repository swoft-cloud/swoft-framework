<?php

namespace Swoft\Router\Service;

use Psr\Http\Message\ServerRequestInterface;
use Swoft\App;
use Swoft\Base\Response;
use Swoft\Bean\Annotation\Bean;
use Swoft\Helper\PhpHelper;
use Swoft\Helper\ResponseHelper;
use Swoft\Middleware\Service\PackerMiddleware;
use Swoft\Router\HandlerAdapterInterface;

/**
 * service handler adapter
 *
 * @Bean("serviceHandlerAdapter")
 * @uses      HandlerAdapterMiddleware
 * @version   2017年11月23日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class HandlerAdapter implements HandlerAdapterInterface
{
    /**
     * the result of service handler
     */
    const ATTRIBUTE = 'serviceResult';

    /**
     * execute service handler
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param array                                    $handler
     *
     * @return Response
     */
    public function doHandler(ServerRequestInterface $request, array $handler)
    {
        // the function params of service
        $data   = $request->getAttribute(PackerMiddleware::ATTRIBUTE_DATA);
        $params = $data['params']?? [];

        list($serviceClass, $method) = $handler;
        $service = App::getBean($serviceClass);

        // execute handler with params
        $response = PhpHelper::call([$service, $method], $params);
        $response = ResponseHelper::formatData($response);

        // response
        if (!$response instanceof Response) {
            $response = (new Response())->withAttribute(self::ATTRIBUTE, $response);
        }

        return $response;
    }
}
