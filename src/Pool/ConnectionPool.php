<?php

namespace Swoft\Pool;

use Swoft\App;
use Swoft\Exception\ConnectionException;
use Swoole\Coroutine\Channel;

/**
 * Class ConnectPool
 */
abstract class ConnectionPool implements PoolInterface
{
    /**
     * Current connection count
     *
     * @var int
     */
    protected $currentCount = 0;

    /**
     * Pool config
     *
     * @var PoolConfigInterface
     */
    protected $poolConfig;

    /**
     * @var Channel
     */
    protected $channel;

    /**
     * @var \SplQueue
     */
    protected $queue;

    /**
     * Initialization
     */
    public function init()
    {
        if (App::isWorkerStatus()) {
            $this->channel = new Channel($this->poolConfig->getMaxActive());
        } else {
            $this->queue = new \SplQueue();
        }
    }

    /**
     * Get connection
     *
     * @throws ConnectionException;
     * @return ConnectionInterface
     */
    public function getConnection():ConnectionInterface
    {
        if (App::isCoContext()) {
            $connection = $this->getConnectionByChannel();
        } else {
            $connection = $this->getConnectionByQueue();
        }

        if ($connection->check() == false) {
            $connection->reconnect();
        }

        return $connection;
    }

    /**
     * Release connection
     *
     * @param ConnectionInterface $connection
     */
    public function release(ConnectionInterface $connection)
    {
        if (App::isCoContext()) {
            $this->releaseToChannel($connection);
        } else {
            $this->releaseToQueue($connection);
        }
    }

    /**
     * Get one address
     *
     * @return string "127.0.0.1:88"
     */
    public function getConnectionAddress()
    {
        $serviceList  = $this->getServiceList();
        $balancerType = $this->poolConfig->getBalancer();
        $balancer     = balancer()->select($balancerType);

        return $balancer->select($serviceList);
    }

    /**
     * Get service list
     *
     * @return array
     * <pre>
     * [
     *   "127.0.0.1:88",
     *   "127.0.0.1:88"
     * ]
     * </pre>
     */
    protected function getServiceList()
    {
        $name = $this->poolConfig->getName();
        if ($this->poolConfig->isUseProvider()) {
            $type = $this->poolConfig->getProvider();

            return provider()->select($type)->getServiceList($name);
        }

        $uri = $this->poolConfig->getUri();
        if (empty($uri)) {
            $error = sprintf('Service does not configure uri name=%s', $name);
            App::error($error);
            throw new \InvalidArgumentException($error);
        }

        return $uri;
    }

    /**
     * @return int
     */
    public function getTimeout(): int
    {
        return $this->poolConfig->getTimeout();
    }

    /**
     * @return PoolConfigInterface
     */
    public function getPoolConfig(): PoolConfigInterface
    {
        return $this->poolConfig;
    }

    /**
     * Release to queue
     *
     * @param $connection
     */
    private function releaseToQueue(ConnectionInterface $connection)
    {
        if ($this->queue->count() < $this->poolConfig->getMaxActive()) {
            $connection->updateLastTime();
            $this->queue->push($connection);
        }
    }

    /**
     * Release to channel
     *
     * @param $connection
     */
    private function releaseToChannel(ConnectionInterface $connection)
    {
        $stats     = $this->channel->stats();
        $maxActive = $this->poolConfig->getMaxActive();
        if ($stats['queue_num'] < $maxActive) {
            $connection->updateLastTime();
            $this->channel->push($connection);
        }
    }

    /**
     * Get connection by queue
     *
     * @return ConnectionInterface
     * @throws ConnectionException
     */
    private function getConnectionByQueue(): ConnectionInterface
    {
        if (!$this->queue->isEmpty()) {
            return $this->getEffectiveConnection($this->queue->count(), false);
        }

        if ($this->currentCount >= $this->poolConfig->getMaxActive()) {
            throw new ConnectionException('Connection pool queue is full');
        }

        $connect = $this->createConnection();
        $this->currentCount++;

        return $connect;
    }

    /***
     * Get connection by channel
     *
     * @return ConnectionInterface
     * @throws ConnectionException
     */
    private function getConnectionByChannel(): ConnectionInterface
    {
        if($this->channel === null){
            $this->channel = new Channel($this->poolConfig->getMaxActive());
        }

        $stats = $this->channel->stats();
        if ($stats['queue_num'] > 0) {
            return $this->getEffectiveConnection($stats['queue_num']);
        }

        $maxActive = $this->poolConfig->getMaxActive();
        if ($this->currentCount < $maxActive) {
            $connection = $this->createConnection();
            $this->currentCount++;

            return $connection;
        }

        $maxWait = $this->poolConfig->getMaxWait();

        if ($maxWait != 0 && $stats['consumer_num'] >= $maxWait) {
            throw new ConnectionException('Connection pool waiting queue is full');
        }

        $writes = [];
        $reads       = [$this->channel];
        $maxWaitTime = $this->poolConfig->getMaxWaitTime();
        $result      = $this->channel->select($reads, $writes, $maxWaitTime);

        if ($result === false || empty($reads)) {
            throw new ConnectionException('Connection pool waiting queue timeout, timeout='.$maxWaitTime);
        }

        $readChannel = $reads[0];

        return $readChannel->pop();
    }

    /**
     * Get effective connection
     *
     * @param int  $queueNum
     * @param bool $isChannel
     *
     * @return ConnectionInterface
     */
    private function getEffectiveConnection(int $queueNum, bool $isChannel = true): ConnectionInterface
    {
        $minActive = $this->poolConfig->getMinActive();
        if ($queueNum <= $minActive) {
            return $this->getOriginalConnection($isChannel);
        }

        $time        = time();
        $moreActive  = $queueNum - $minActive;
        $maxWaitTime = $this->poolConfig->getMaxWaitTime();
        for ($i = 0; $i < $moreActive; $i++) {
            /* @var ConnectionInterface $connection */
            $connection = $this->getOriginalConnection($isChannel);;
            $lastTime = $connection->getLastTime();
            if ($time - $lastTime < $maxWaitTime) {
                return $connection;
            }
            $this->currentCount--;
        }

        return $this->getOriginalConnection($isChannel);
    }

    /**
     * Get original connection
     *
     * @param bool $isChannel
     *
     * @return ConnectionInterface
     */
    private function getOriginalConnection(bool $isChannel = true): ConnectionInterface
    {
        if ($isChannel) {
            return $this->channel->pop();
        }

        return $this->queue->shift();
    }
}
