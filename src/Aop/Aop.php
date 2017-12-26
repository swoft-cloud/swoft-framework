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
     *
     * @return mixed;
     */
    public function execute($target, string $method, array $params)
    {
        $class = get_parent_class($target);
        if (!isset($this->map[$class][$method]) || empty($this->map[$class][$method])) {
            return $target->$method(...$params);
        }

        $this->match(Aop::class, $class, $method, []);
        $advices = $this->map[$class][$method];
        return $this->doAdvice($target, $method, $params, $advices);
    }

    private function doAdvice($target, string $method, array $params, array $advices)
    {
        $result = null;

        $advice = array_shift($advices);
        try {
            if (isset($advice['around']) && !empty($advice['around'])) {
                list($aspectClass, $aspectMethod) = $advice['around'];
                $proceedingJoinPoint = new ProceedingJoinPoint($target, $method, $params, $advice);

                $result = $aspectClass->$aspectMethod($proceedingJoinPoint);
            } else {
                // before
                if ($advice['before'] && !empty($advice['before'])) {
                    list($aspectClass, $aspectMethod) = $advice['before'];
                    $aspectClass->$aspectMethod();
                }
                $result = $target->$method(...$params);
            }

            if (!empty($advices)) {
                return $this->doAdvice($target, $method, $params, $advices);
            }

            if (isset($advice['after']) && !empty($advice['after'])) {
                list($aspectClass, $aspectMethod) = $advice['after'];
                $aspectClass->$aspectMethod();
            }
        } catch (\Exception $e) {
            if (isset($advice['afterThrowing']) && !empty($advice['afterThrowing'])) {
                list($aspectClass, $aspectMethod) = $advice['afterThrowing'];

                return $aspectClass->$aspectMethod();
            }
        }

        if (isset($advice['afterReturning']) && !empty($advice['afterReturning'])) {
            list($aspectClass, $aspectMethod) = $advice['afterReturning'];

            return $aspectClass->$aspectMethod();
        }

        return $result;
    }

    public function match(string $beanName, string $class, string $method, array $annotations)
    {
        $advices = [];
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
                $advices[] = $aspect['advice'];
            }
        }

        $this->map[$class][$method] = $advices;

        return $advices;
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