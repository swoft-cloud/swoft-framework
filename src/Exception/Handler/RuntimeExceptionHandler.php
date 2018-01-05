<?php

namespace Swoft\Exception\Handler;

/**
 * runtime exception handler
 *
 * @uses      RuntimeExceptionHandler
 * @version   2017-11-10
 * @author    huangzhhui <huangzhwork@gmail.com>
 * @copyright Copyright 2010-2017 Swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class RuntimeExceptionHandler extends AbstractHandler
{

    /**
     * @return bool
     */
    public function isHandle(): bool
    {
        return $this->getException() instanceof \RuntimeException;
    }

    /**
     * @return \Swoft\Web\Response
     */
    public function handle()
    {
        $code = $this->getException()->getCode() ? : 500;
        return $this->setStatusCode($code);
    }
}
