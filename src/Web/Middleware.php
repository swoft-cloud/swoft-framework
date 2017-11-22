<?php

namespace Swoft\Web;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Swoft\Base\RequestContext;
use Swoft\Web\Middlewares\MiddlewareInterface;
use Swoft\Web\Middlewares\PowerByMiddlewre;


/**
 * @uses      Middleware
 * @version   2017年11月14日
 * @author    huangzhhui <huangzhwork@gmail.com>
 * @copyright Copyright 2010-2017 Swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class Middleware
{

    /**
     * Global middlewares
     *
     * @var array
     */
    protected $middlewares = [
        PowerByMiddlewre::class,
    ];

    public function handle(ServerRequestInterface $request, ResponseInterface $response)
    {
        $matchMiddlewares = RequestContext::getContextDataByKey('middlewares', []);
        foreach ((array)$matchMiddlewares as $middleware) {
            if ($middleware instanceof MiddlewareInterface) {
                $handler = new Handler();
                $response = $middleware->process($request, $handler);
            }
        }

    }

}