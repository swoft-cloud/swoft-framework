<?php

namespace Swoft\Core;

use Swoft\Pool\ConnectionInterface;
use Swoft\Pool\PoolInterface;

/**
 * The result of cor
 */
abstract class AbstractCoResult implements ResultInterface
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
     * @var string
     */
    protected $profileKey;

    /**
     * AbstractCorResult constructor.
     *
     * @param mixed         $connection
     * @param string        $profileKey
     * @param PoolInterface $pool
     */
    public function __construct(ConnectionInterface $connection = null, string $profileKey = '', PoolInterface $pool = null)
    {
        $this->pool       = $pool;
        $this->connection = $connection;
        $this->profileKey = $profileKey;
    }

    /**
     * Receive by defer
     *
     * @param bool $defer
     *
     * @return mixed
     */
    public function recv($defer = false)
    {
        $result = $this->connection->recv();

        // 重置延迟设置
        if ($defer) {
            $this->connection->setDefer(false);
        }

        if ($this->pool !== null) {
            $this->pool->release($this->connection);
        }

        return $result;
    }
}