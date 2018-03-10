<?php

namespace Swoft\Pool;

/**
 * Interface ConnectInterface
 *
 * @package Swoft\Pool
 */
interface ConnectionInterface
{
    /**
     * Create connectioin
     *
     * @return void
     */
    public function createConnection();

    /**
     * Reconnect
     */
    public function reconnect();

    /**
     * @return bool
     */
    public function check(): bool;

    /**
     * @return int
     */
    public function getLastTime(): int;

    /**
     * @return void
     */
    public function updateLastTime();
}
