<?php

namespace Swoft\Core;

use Swoft\Pool\ConnectionInterface;
use Swoft\Pool\PoolInterface;

/**
 * Sync result
 */
abstract class AbstractDataResult implements ResultInterface
{
    /**
     * @var ConnectionInterface
     */
    protected $connection;

    /**
     * @var PoolInterface
     */
    protected $pool;

    /**
     * @var mixed
     */
    protected $data;

    /**
     * AbstractDataResult constructor.
     *
     * @param mixed               $data
     * @param ConnectionInterface $connection
     * @param PoolInterface       $pool
     */
    public function __construct($data, ConnectionInterface $connection = null, PoolInterface $pool = null)
    {
        $this->data       = $data;
        $this->pool       = $pool;
        $this->connection = $connection;
    }
}