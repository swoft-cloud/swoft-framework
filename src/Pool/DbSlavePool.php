<?php

namespace Swoft\Pool;

use Swoft\Bean\Annotation\Inject;
use Swoft\Bean\Annotation\Pool;
use Swoft\Pool\Config\DbSlavePoolConfig;

/**
 * the pool of slave
 *
 * @Pool()
 * @uses      DbSlavePool
 * @version   2017年12月17日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class DbSlavePool extends DbPool
{
    /**
     * @Inject()
     * @var DbSlavePoolConfig
     */
    protected $poolConfig;
}