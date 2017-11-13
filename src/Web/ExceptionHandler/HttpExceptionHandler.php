<?php

namespace Swoft\Web\ExceptionHandler;

use Swoft\Exception\Http\HttpException;
use Swoft\Exception\Http\Unauthorized;
use Swoft\Web\Response;

/**
 * @uses      HttpExceptionHandler
 * @version   2017-11-11
 * @author    huangzhhui <huangzhwork@gmail.com>
 * @copyright Copyright 2010-2017 Swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class HttpExceptionHandler extends AbstractHandler
{

    /**
     * This handler should handle the exception ?
     *
     * @return bool
     */
    public function isHandle(): bool
    {
        return $this->getException() instanceof HttpException;
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
        $exception->getMessage() && $this->setMessage($exception);
        return $this;
    }
}