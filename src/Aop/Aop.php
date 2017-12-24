<?php

namespace Swoft\Aop;

use Swoft\Bean\Annotation\Bean;

/**
 * the class of aop
 *
 * @Bean()
 * @uses      Aop
 * @version   2017年12月24日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class Aop implements AopInterface
{
    /**
     * @var array
     */
    private $map
        = [

        ];

    /**
     * @var array
     */
    private $aspects = [];

    /**
     * @param object $target
     * @param string $method
     * @param array  $params
     */
    public function execute($target, string $method, array $params)
    {

        $around = [];

        $p = new ProceedingJoinPoint();
        // around excute
        $this->around(new ProceedingJoinPoint());


        $p->proceed();

        // after

        // afterReturning/afterThrowning

    }

    public function around(ProceedingJoinPoint $point)
    {
        // around before

        $point->proceed();

        // around after
    }

    public function match(string $beanName, string $class, string $method, array $annotations)
    {
        foreach ($this->aspects as $aspectClass => $aspect) {
            if (!isset($aspect['point']) || !isset($aspect['advice'])) {
                continue;
            }

            $pointBeanInclude       = $aspect['point']['bean']['include']?? [];
            $pointAnnotationInclude = $aspect['point']['annotation']['include']?? [];
            $pointExecutionInclude  = $aspect['point']['execution']['include']?? [];

            $pointBeanExclude       = $aspect['point']['bean']['exclude']?? [];
            $pointAnnotationExclude = $aspect['point']['annotation']['exclude']?? [];
            $pointExecutionExclude  = $aspect['point']['execution']['exclude']?? [];

            $includeMath = $this->matchBeanAndAnnotation([$beanName], $pointBeanInclude)
                || $this->matchBeanAndAnnotation($annotations, $pointAnnotationInclude)
                || $this->matchExecution($class, $method, $pointExecutionInclude);

            $excludeMath = $this->matchBeanAndAnnotation([$beanName], $pointBeanExclude)
                || $this->matchBeanAndAnnotation($annotations, $pointAnnotationExclude)
                || $this->matchExecution($class, $method, $pointExecutionExclude);

            if ($includeMath && !$excludeMath) {
                $this->map[$class][$method][] = $aspect['advice'];
            }
        }
    }

    public function register(array $aspects)
    {
        array_multisort(array_column($aspects, 'order'), SORT_ASC, $aspects);
        $this->aspects = $aspects;
    }

    private function matchBeanAndAnnotation(array $pointAry, array $classAry): bool
    {
        $intersectAry = array_intersect($pointAry, $classAry);
        if (empty($intersectAry)) {
            return false;
        }

        return true;
    }

    private function matchExecution(string $class, $method, array $executions): bool
    {
        $methodPath = $class . "\\" . $method;
        foreach ($executions as $execution) {
            $reg = sprintf('/^%s$/', $execution);
            if (preg_match($reg, $methodPath)) {
                return true;
            }
        }

        return false;
    }
}