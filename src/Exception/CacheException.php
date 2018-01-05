<?php

namespace Swoft\Exception;

use Psr\SimpleCache\InvalidArgumentException;

/**
 * the exception of cache
 *
 * @uses      CacheException
 * @version   2017年12月24日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class CacheException extends \Exception implements InvalidArgumentException
{
}
