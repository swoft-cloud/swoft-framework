<?php

namespace Swoft\Pool;

/**
 * Class AbstractConnect
 */
abstract class AbstractConnection implements ConnectionInterface
{
    /**
     * @var PoolInterface
     */
    protected $pool;

    /**
     * @var int
     */
    protected $lastTime;

    /**
     * AbstractConnection constructor.
     *
     * @param PoolInterface $connectPool
     */
    public function __construct(PoolInterface $connectPool)
    {
        $this->lastTime = time();
        $this->pool     = $connectPool;
        $this->createConnection();
    }

    /**
     * @return int
     */
    public function getLastTime(): int
    {
        return $this->lastTime;
    }

    /**
     * Update last time
     */
    public function updateLastTime()
    {
        $this->lastTime = time();
    }
}
