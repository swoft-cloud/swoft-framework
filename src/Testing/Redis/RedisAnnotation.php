<?php

namespace Swoft\Testing\Redis;

use Swoft\Bean\Annotation\Cacheable;

/**
 * the test of redis
 *
 * @uses      RedisAnnotation
 * @version   2018年01月01日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class RedisAnnotation
{
    /**
     * get data from cache or update
     *
     * @Cacheable()
     * @param mixed $id
     */
    public function findById($id)
    {
    }
}
