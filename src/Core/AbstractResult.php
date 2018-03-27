<?php

namespace Swoft\Core;

use Swoft\Pool\ConnectionInterface;

/**
 * AbstractResult
 */
abstract class AbstractResult implements ResultInterface
{
    /**
     * @var ConnectionInterface
     */
    protected $connection;

    /**
     * @var string
     */
    protected $profileKey;

    /**
     * @var mixed
     */
    protected $result;

    /**
     * AbstractCorResult constructor.
     *
     * @param mixed  $result
     * @param mixed  $connection
     * @param string $profileKey
     */
    public function __construct($result, $connection = null, string $profileKey = '')
    {
        $this->result     = $result;
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
    protected function recv($defer = false)
    {
        if ($this->connection instanceof ConnectionInterface) {
            $result = $this->connection->receive();
            $this->release();

            return $result;
        }

        $result = $this->connection->recv();
        if ($defer) {
            $this->connection->setDefer(false);
        }

        return $result;
    }

    /**
     * @return void
     */
    protected function release()
    {
        if ($this->connection instanceof ConnectionInterface) {
            $this->connection->release();
        }
    }
}