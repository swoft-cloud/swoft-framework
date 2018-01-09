<?php

namespace Swoft\Exception\Handler;

/**
 * system error handler
 *
 * @uses      SystemErrorHandler
 * @version   2017-11-10
 * @author    huangzhhui <huangzhwork@gmail.com>
 * @copyright Copyright 2010-2017 Swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class SystemErrorHandler extends AbstractHandler
{

    /**
     * @return bool
     */
    public function isHandle(): bool
    {
        return true;
    }

    /**
     * @return \Swoft\Web\Response
     */
    public function handle()
    {
        return $this->setStatusCode(500);
    }
}
