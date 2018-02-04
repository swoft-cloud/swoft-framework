<?php

namespace Swoft\Core;

/**
 * Dispatcher
 */
interface DispatcherInterface
{
    /**
     * Dispatch
     *
     * @param array ...$params dispatcher params
     */
    public function dispatch(...$params);

    /**
     * Request middleware
     *
     * @return array
     */
    public function requestMiddleware(): array;

    /**
     * Pre middleware
     *
     * @return array
     */
    public function preMiddleware(): array;

    /**
     * After middleware
     *
     * @return array
     */
    public function afterMiddleware(): array;
}
