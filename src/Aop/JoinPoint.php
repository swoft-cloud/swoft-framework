<?php

namespace Swoft\Aop;

/**
 * the join point of class
 *
 * @uses      JoinPoint
 * @version   2017年12月25日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class JoinPoint implements JoinPointInterface
{
    /**
     * @var array
     */
    private $args = [];

    /**
     * @var object
     */
    private $target;

    /**
     * JoinPoint constructor.
     *
     * @param array  $args
     * @param object $target
     */
    public function __construct(array $args, $target)
    {
        $this->args   = $args;
        $this->target = $target;
    }

    /**
     * @return array
     */
    public function getArgs(): array
    {
        return $this->args;
    }

    /**
     * @return object
     */
    public function getTarget()
    {
        return $this->target;
    }
}