<?php

namespace Swoft\Base;

/**
 * dispatcher interface
 *
 * @uses      DispatcherInterface
 * @version   2017年11月23日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
interface DispatcherInterface
{
    /**
     * do dispatcher
     *
     * @param array ...$params  dispatcher params
     */
    public function doDispatcher(...$params);

    /**
     * request middlewares
     *
     * @return array
     */
    public function requestMiddlewares();

    /**
     * the first middleware of request
     *
     * @return array
     */
    public function firstMiddleware();

    /**
     * the last middleware of request
     *
     * @return array
     */
    public function lastMiddleware();
}