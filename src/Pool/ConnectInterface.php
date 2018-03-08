<?php

namespace Swoft\Pool;

/**
 * Interface ConnectInterface
 *
 * @package Swoft\Pool
 */
interface ConnectInterface
{
    /**
     * 创建连接
     *
     * @return mixed
     */
    public function createConnect();

    /**
     * 重新连接
     */
    public function reConnect();
}
