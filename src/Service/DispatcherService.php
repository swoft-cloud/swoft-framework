<?php

namespace Swoft\Service;

use Swoft\Base\DispatcherInterface;
use Swoole\Server;

/**
 * service dispatcher
 *
 * @uses      DispatcherService
 * @version   2017年11月23日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class DispatcherService implements DispatcherInterface
{
    /**
     * service middlewares
     *
     * @var array
     */
    private $middlewares = [];

    /**
     * do dispatcher
     *
     * @param array ...$params
     */
    public function doDispatcher(...$params)
    {
        /**
         * @var Server $server
         * @var int    $fd
         * @var int    $fromid
         * @var string $data
         */
        list($server, $fd, $fromid, $data) = $params;


    }

    public function requestMiddlewares()
    {
        // TODO: Implement requestMiddlewares() method.
    }

    public function firstMiddlewares()
    {
        // TODO: Implement firstMiddlewares() method.
    }

    public function lastMiddlewares()
    {
        // TODO: Implement lastMiddlewares() method.
    }
}