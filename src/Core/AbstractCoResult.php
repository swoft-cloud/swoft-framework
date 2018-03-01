<?php

namespace Swoft\Core;

use Swoft\Pool\ConnectPool;

/**
 * The result of cor
 */
abstract class AbstractCoResult implements ResultInterface
{
    /**
     * @var object
     */
    protected $client;

    /**
     * @var ConnectPool
     */
    protected $connectPool;

    /**
     * @var string
     */
    protected $profileKey;

    /**
     * AbstractCorResult constructor.
     *
     * @param mixed       $client
     * @param string      $profileKey
     * @param ConnectPool $connectPool
     */
    public function __construct($client = null, string $profileKey = '', ConnectPool $connectPool = null)
    {
        $this->client      = $client;
        $this->profileKey  = $profileKey;
        $this->connectPool = $connectPool;
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
        $result = $this->client->recv();

        // 重置延迟设置
        if ($defer) {
            $this->client->setDefer(false);
        }

        if ($this->connectPool !== null) {
            $this->connectPool->release($this->client);
        }

        return $result;
    }
}