<?php

namespace Swoft\Db\Pool;

use Swoft\Bean\Annotation\Inject;
use Swoft\Bean\Annotation\Pool;
use Swoft\Db\Pool\Config\DbSlavePoolConfig;

/**
 * Slave pool
 *
 * @Pool("default.slave")
 */
class DbSlavePool extends DbPool
{
    /**
     * @Inject()
     * @var DbSlavePoolConfig
     */
    protected $poolConfig;
}
