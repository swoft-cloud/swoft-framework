<?php

namespace Swoft\Cache;

use Swoft\Bean\Annotation\Bean;
use Swoft\Cache\Redis\CacheRedis;

/**
 * the interface of cache
 *
 * @Bean(ref=CacheRedis::class)
 * @uses      CacheInterface
 * @version   2017年12月24日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
interface CacheInterface extends \Psr\SimpleCache\CacheInterface
{
}
