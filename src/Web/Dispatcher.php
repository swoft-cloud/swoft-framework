<?php

namespace Swoft\Web;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Swoft\Bean\Annotation\Bean;
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
     * @var array
     */
    protected $middlewares = [];

    /**
     * @var string
     */
    protected $defaultHandler = ControllerMiddleware::class;

    /**
     * Dispatcher constructor.
     *
     * @param array $middlewares
     */
    public function __construct(array $middlewares = [])
    {
        $this->middlewares = $middlewares;
    }

    /**
     * Dispatch the request
     *
     * @param ServerRequestInterface $request
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function dispatch(ServerRequestInterface $request): ResponseInterface
    {
        $response = (new RequestHandler($this->middlewares, $this->getDefaultHandler()))->handle($request);
        return $response;
    }

    /**
     * @return string
     */
    public function getDefaultHandler(): string
    {
        return $this->defaultHandler;
    }

    /**
     * @param string $defaultHandler
     * @return Dispatcher
     */
    public function setDefaultHandler(string $defaultHandler)
    {
        $this->defaultHandler = $defaultHandler;
        return $this;
    }

}