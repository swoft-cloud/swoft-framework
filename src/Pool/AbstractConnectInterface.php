<?php

namespace Swoft\Pool;

/**
 *
 *
 * @uses      AbstractConnectInterface
 * @version   2017年09月28日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
abstract class AbstractConnectInterface implements ConnectInterface
{
    /**
     * @var ConnectPoolInterface
     */
    protected $connectPool;

    public function __construct(ConnectPoolInterface $connectPool)
    {
        $this->connectPool = $connectPool;
        $this->createConnect();
    }
}
