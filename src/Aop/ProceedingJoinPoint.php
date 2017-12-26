<?php

namespace Swoft\Aop;

/**
 * the proceedingJoinPoint of class
 *
 * @uses      ProceedingJoinPoint
 * @version   2017年12月24日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class ProceedingJoinPoint implements ProceedingJoinPointInterface
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
     * @var string
     */
    private $method;

    /**
     * @var array
     */
    private $advice;

    /**
     * ProceedingJoinPoint constructor.
     *
     * @param        $target
     * @param string $method
     * @param array  $args
     * @param array  $advice
     */
    public function __construct($target, string $method, array $args, array $advice)
    {
        $this->target = $target;
        $this->method = $method;
        $this->args   = $args;
        $this->advice = $advice;
    }

    public function proceed()
    {
        // before
        if($this->advice['before'] && !empty($this->advice['before'])){
            list($aspectClass, $aspectMethod) = $this->advice['before'];
            $aspectClass->$aspectMethod();
        }

        // execute
        return $this->target->{$this->method}(...$this->args);
    }

    public function reProceed(array $args = [])
    {
        // TODO: Implement reProceed() method.
    }


    public function getArgs(): array
    {
        // TODO: Implement getArgs() method.
    }

    public function getTarget()
    {
        // TODO: Implement getTarget() method.
    }


}