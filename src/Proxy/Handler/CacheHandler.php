<?php

namespace Swoft\Proxy\Handler;

/**
 *
 *
 * @uses      CacheHandler
 * @version   2017年12月23日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class CacheHandler implements HandlerInterface
{
    /**
     * @var object
     */
    private $target;

    public function __construct($target)
    {
        $this->target = $target;
    }

    public function invoke($method, $parameters)
    {
        return $this->target->$method(...$parameters);
    }
}