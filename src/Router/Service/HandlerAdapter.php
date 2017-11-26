<?php

namespace Swoft\Router\Service;

use Psr\Http\Message\ServerRequestInterface;
use Swoft\Router\HandlerAdapterInterface;
use Swoft\Router\HandlerInterface;

/**
 *
 *
 * @uses      HandlerAdapterMiddleware
 * @version   2017年11月23日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class HandlerAdapter implements HandlerAdapterInterface
{
    public function doHandler(ServerRequestInterface $request, array $handler)
    {
        // TODO: Implement doHandler() method.
    }
}