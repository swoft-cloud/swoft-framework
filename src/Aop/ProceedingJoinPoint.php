<?php

namespace Swoft\Aop;

use Swoft\App;

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

    private $advices;

    /**
     * ProceedingJoinPoint constructor.
     *
     * @param        $target
     * @param string $method
     * @param array  $args
     * @param array  $advice
     * @param array  $advices
     */
    public function __construct($target, string $method, array $args, array $advice, array $advices)
    {
        $this->target  = $target;
        $this->method  = $method;
        $this->args    = $args;
        $this->advice  = $advice;
        $this->advices = $advices;
    }

    public function proceed()
    {
        // before
        if ($this->advice['before'] && !empty($this->advice['before'])) {
            list($aspectClass, $aspectMethod) = $this->advice['before'];
            $aspect = App::getBean($aspectClass);
            $aspect->$aspectMethod();
        }

        // execute
        $result = $this->target->{$this->method}(...$this->args);
        if(!empty($this->advices)){
            /* @var \Swoft\Aop\Aop $aop*/
            $aop = App::getBean(Aop::class);
            $result = $aop->doAdvice($this->target, $this->method, $this->args, $this->advices);
        }

        return $result;
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