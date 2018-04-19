<?php

namespace Swoft\Db;

/**
 * The type of pool
 */
class Pool
{
    /**
     * Default group
     */
    const GROUP = 'default';

    /**
     * The master
     */
    const MASTER = 'master';

    /**
     * The slave
     */
    const SLAVE = 'slave';

}