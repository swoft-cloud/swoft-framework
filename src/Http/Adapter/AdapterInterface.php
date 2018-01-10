<?php

namespace Swoft\Http\Adapter;

use Psr\Http\Message\RequestInterface;
use Swoft\Http\HttpResult;

/**
 * Http client adapter interface
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
