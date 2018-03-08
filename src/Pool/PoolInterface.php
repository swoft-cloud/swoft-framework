<?php

namespace Swoft\Pool;

/**
 * Interface PoolInterface
 *
 * @package Swoft\Pool
 */
interface PoolInterface
{
    /**
     * Get a connection
     *
     * @return mixed
     */
    public function getConnect();

    /**
     * Relesea the connection
     *
     * @param object $connect
     */
    public function release($connect);
}
