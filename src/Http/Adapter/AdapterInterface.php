<?php

namespace Swoft\Http\Adapter;

use Psr\Http\Message\RequestInterface;
use Swoft\Http\HttpResult;

/**
 * @uses      AdapterInterface
 * @version   2017-11-22
 * @author    huangzhhui <huangzhwork@gmail.com>
 * @copyright Copyright 2010-2017 Swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
interface AdapterInterface
{

    /**
     * Send a http request
     *
     * @param RequestInterface $request
     * @param array $options
     * @return HttpResult
     */
    public function request(RequestInterface $request, array $options = []): HttpResult;

    /**
     * Get the adapter default user agent
     *
     * @return string
     */
    public function getUserAgent(): string;

}