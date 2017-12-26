<?php

namespace Swoft\Aop;

use Swoft\App;
use Swoft\Bean\Annotation\Bean;
use Swoft\Bean\Collector;

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

    public function init()
    {
        $aspects = Collector::$aspects;
        $this->register($aspects);
    }

    /**
     * @param object $target
     * @param string $method
     * @param array  $params
     *
     * @return mixed;
     */
    public function execute($target, string $method, array $params)
    {
        $class = get_class($target);
        if (!isset($this->map[$class][$method]) || empty($this->map[$class][$method])) {
            return $target->$method(...$params);
        }

        $advices = $this->map[$class][$method];
        return $this->doAdvice($target, $method, $params, $advices);
    }

    public function doAdvice($target, string $method, array $params, array $advices)
    {
        $result = null;

        $advice = array_shift($advices);
        try {
            if (isset($advice['around']) && !empty($advice['around'])) {
                list($aspectClass, $aspectMethod) = $advice['around'];
                $proceedingJoinPoint = new ProceedingJoinPoint($target, $method, $params, $advice, $advices);

                $aspect = App::getBean($aspectClass);
                $result = $aspect->$aspectMethod($proceedingJoinPoint);
            } else {
                // before
                if ($advice['before'] && !empty($advice['before'])) {
                    list($aspectClass, $aspectMethod) = $advice['before'];

                    $aspect = App::getBean($aspectClass);
                    $aspect->$aspectMethod();
                }
                $result = $target->$method(...$params);
            }

            if (isset($advice['after']) && !empty($advice['after'])) {
                list($aspectClass, $aspectMethod) = $advice['after'];
                $aspect = App::getBean($aspectClass);
                $aspect->$aspectMethod();
            }
        } catch (\Exception $e) {
            if (isset($advice['afterThrowing']) && !empty($advice['afterThrowing'])) {
                list($aspectClass, $aspectMethod) = $advice['afterThrowing'];
                $aspect = App::getBean($aspectClass);

                return $aspect->$aspectMethod();
            }
        }

        if (isset($advice['afterReturning']) && !empty($advice['afterReturning'])) {
            list($aspectClass, $aspectMethod) = $advice['afterReturning'];
            $aspect = App::getBean($aspectClass);

            return $aspect->$aspectMethod();
        }

        return $result;
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