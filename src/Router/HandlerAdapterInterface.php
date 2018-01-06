<?php

namespace Swoft\Router;

use Psr\Http\Message\ServerRequestInterface;

/**
 * handler adapter interface
 *
 * @uses      HandlerAdapterInterface
 * @version   2017年11月23日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
interface HandlerAdapterInterface
{
    /**
     * execute handler
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param array                                    $handler
     *
     * @return \Swoft\Core\Response
     */
    public function doHandler(ServerRequestInterface $request, array $handler);
}
