<?php

namespace Swoft\Web;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Swoft\Bean\Annotation\Bean;
use Swoft\Bean\Annotation\Inject;
use Swoft\Web\Middlewares\ControllerMiddleware;
use Swoft\Web\Middlewares\MiddlewareCollection;
use Swoft\Web\Middlewares\RequestHandler;

/**
 * @Bean()
 * @uses      Dispatcher
 * @version   2017年11月14日
 * @author    huangzhhui <huangzhwork@gmail.com>
 * @copyright Copyright 2010-2017 Swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class Dispatcher
{

    /**
     * @var string
     */
    protected $defaultHandler = ControllerMiddleware::class;

    /**
     * Dispatch the request
     *
     * @param ServerRequestInterface $request
     * @param array $middlewares
     * @return ResponseInterface
     */
    public function dispatch(ServerRequestInterface $request, $middlewares = []): ResponseInterface
    {
        $response = (new RequestHandler($middlewares, $this->defaultHandler))->handle($request);
        return $response;
    }
}
