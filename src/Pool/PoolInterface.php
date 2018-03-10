<?php

namespace Swoft\Pool;

/**
 * Interface PoolInterface
 */
interface PoolInterface
{
    /**
     * @return ConnectionInterface
     */
    public function createConnection(): ConnectionInterface;

    /**
     * Get a connection
     *
     * @return ConnectionInterface
     */
    public function getConnection(): ConnectionInterface;

    /**
     * Relesea the connection
     *
     * @param ConnectionInterface $connection
     */
    public function release(ConnectionInterface $connection);
}
