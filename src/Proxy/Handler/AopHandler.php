<?php

namespace Swoft\Proxy\Handler;

/**
 * the handler of aop
 *
 * @uses      AopHandler
 * @version   2017年12月24日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class AopHandler implements HandlerInterface
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