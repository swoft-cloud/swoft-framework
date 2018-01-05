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
    protected $args = [];

    /**
     * @var object
     */
    protected $target;

    /**
     * @var mixed
     */
    protected $return;

    /**
     * @var string
     */
    protected $method;

    /**
     * JoinPoint constructor.
     *
     * @param object $target the object of origin
     * @param string $mehtod the method of origin
     * @param array  $args   the params of method
     * @param mixed  $return the return of executed method
     */
    public function __construct($target, string $mehtod, array $args, $return = null)
    {
        $this->args   = $args;
        $this->return = $return;
        $this->target = $target;
        $this->method = $mehtod;
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

    /**
     * @return mixed
     */
    public function getReturn()
    {
        return $this->return;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }
}
