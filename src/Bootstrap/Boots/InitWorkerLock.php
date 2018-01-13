<?php

namespace Swoft\Bootstrap\Boots;

use Swoft\App;
use Swoft\Bean\Annotation\Bootstrap;
use Swoole\Lock;
use Swoft\Bootstrap\Server\AbstractServer;

/**
 * @Bootstrap(order=4)
 * @uses      InitWorkerLock
 * @version   2017-11-02
 * @author    huangzhhui <huangzhwork@gmail.com>
 * @copyright Copyright 2010-2017 Swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class InitWorkerLock implements Bootable
{
    public function bootstrap()
    {
        $server = App::$server;
        if ($server instanceof AbstractServer) {
            $server->setWorkerLock(new Lock(SWOOLE_RWLOCK));
        }
    }
}
