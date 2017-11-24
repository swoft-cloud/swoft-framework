<?php

namespace Swoft\Http\Adapter;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

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
     * @param RequestInterface $request
     * @param array $options
     * @return ResponseInterface
     */
    public function request(RequestInterface $request, array $options = []);

    /**
     * @param RequestInterface $request
     * @param array $options
     * @return ResponseInterface
     */
    public function requestDefer(RequestInterface $request, array $options = []);

}