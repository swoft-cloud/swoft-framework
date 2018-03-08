<?php

namespace Swoft\Pool;

/**
 * Class AbstractConnect
 *
 * @package Swoft\Pool
 */
abstract class AbstractConnect implements ConnectInterface
{
    /**
     * @var ConnectPool
     */
    protected $connectPool;

    public function __construct(ConnectPool $connectPool)
    {
        $this->connectPool = $connectPool;
        $this->createConnect();
    }
}
