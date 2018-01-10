<?php

namespace Swoft\Core;

use Swoft\App;
use Swoft\Pool\ConnectPoolInterface;

/**
 * 基类结果
 *
 * @uses      AbstractResult
 * @version   2017年07月15日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 Swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
abstract class AbstractResult implements ResultInterface
{
    /**
     * @var ConnectPoolInterface 连接池
     */
    protected $connectPool;

    /**
     * @var mixed 连接client
     */
    protected $client;

    /**
     * @var string 缓存性能统计KEY
     */
    protected $profileKey;

    /**
     * @var bool 延迟请求是否发送成功
     */
    protected $sendResult = true;

    protected $release = true;

    /**
     * AbstractResult constructor.
     *
     * @param ConnectPoolInterface $connectPool
     * @param mixed       $client
     * @param string      $profileKey
     * @param bool        $result
     * @param bool        $release
     */
    public function __construct($connectPool, $client, string $profileKey, $result, $release = true)
    {
        $this->connectPool = $connectPool;
        $this->client = $client;
        $this->profileKey = $profileKey;
        $this->sendResult = $result;
        $this->release = $release;
    }

    /**
     * 延迟收包
     *
     * @param bool $defer 是否是延迟收包
     *
     * @return mixed
     */
    public function recv($defer = false)
    {
        App::profileStart($this->profileKey);
        $result = $this->client->recv();
        App::profileEnd($this->profileKey);

        // 重置延迟设置
        if ($defer) {
            $this->client->setDefer(false);
        }

        if ($this->release && $this->connectPool instanceof ConnectPoolInterface) {
            $this->connectPool->release($this->client);
        }
        return $result;
    }
}
