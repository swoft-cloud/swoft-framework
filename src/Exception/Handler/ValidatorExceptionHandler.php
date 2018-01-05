<?php

namespace Swoft\Exception\Handler;

use Swoft\Exception\ValidatorException;

/**
 * validator exception
 *
 * @uses      ValidatorExceptionHandler
 * @version   2017年12月04日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class ValidatorExceptionHandler extends AbstractHandler
{
    /**
     * This handler should handle the exception ?
     *
     * @return bool
     */
    public function isHandle(): bool
    {
        return $this->getException() instanceof ValidatorException;
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
        $exception->getMessage() && $this->setMessage($exception->getMessage());
        return $this;
    }
}
