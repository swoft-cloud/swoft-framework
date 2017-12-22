<?php

namespace Swoft\Exception\Handler;

/**
 * the handler of default exception
 *
 * @uses      DefaultExceptionHandler
 * @version   2017年12月18日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class DefaultExceptionHandler extends AbstractHandler
{
    /**
     * This handler should handle the exception ?
     *
     * @return bool
     */
    public function isHandle(): bool
    {
        return true;
    }

    /**
     * handle the exception and return a Response
     *
     * @return \Swoft\Web\Response
     */
    public function handle()
    {
        $exception = $this->getException();
        $this->setStatusCode($exception->getCode());

        $message = $exception->getMessage();
        $this->setMessage($message);
        return $this;
    }
}