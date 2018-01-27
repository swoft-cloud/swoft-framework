<?php

namespace Swoft\Core;

/**
 * Dispatcher
 */
interface DispatcherInterface
{
    /**
     * do dispatcher
     *
     * @param array ...$params dispatcher params
     */
    public function dispatch(...$params);

    /**
     * request middlewares
     *
     * @return array
     */
    public function requestMiddleware(): array;

    /**
     * the first middleware of request
     *
     * @return array
     */
    public function preMiddleware(): array;

    /**
     * the last middleware of request
     *
     * @return array
     */
    public function afterMiddleware(): array;
}
