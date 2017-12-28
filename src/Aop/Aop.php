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
    private $map = [];

    /**
     * @var array
     */
    private $aspects = [];

    /**
     * init
     */
    public function init()
    {
        // register aop
        $aspects = Collector::$aspects;
        $this->register($aspects);
    }

    /**
     * execute origin method by aop
     *
     * @param object $target the object of origin
     * @param string $method the method of object
     * @param array  $params the params of method
     *
     * @return mixed
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

    /**
     * do advice
     *
     * @param object $target  the object of origin
     * @param string $method  the method of object
     * @param array  $params  the params of method
     * @param array  $advices the advices of aop
     *
     * @return mixed
     */
    public function doAdvice($target, string $method, array $params, array $advices)
    {
        $result = null;
        $advice = array_shift($advices);

        try {

            // around
            if (isset($advice['around']) && !empty($advice['around'])) {
                $result = $this->doPoint($advice['around'], $target, $method, $params, $advice, $advices);
            } else {
                // before
                if ($advice['before'] && !empty($advice['before'])) {
                    $result = $this->doPoint($advice['before'], $target, $method, $params, $advice, $advices);
                }
                $result = $target->$method(...$params);
            }

            // after
            if (isset($advice['after']) && !empty($advice['after'])) {
                $this->doPoint($advice['after'], $target, $method, $params, $advice, $advices, $result);
            }
        } catch (\Exception $e) {
            if (isset($advice['afterThrowing']) && !empty($advice['afterThrowing'])) {
                return $this->doPoint($advice['afterThrowing'], $target, $method, $params, $advice, $advices);;
            }
        }

        // afterReturning
        if (isset($advice['afterReturning']) && !empty($advice['afterReturning'])) {
            return $this->doPoint($advice['afterReturning'], $target, $method, $params, $advice, $advices, $result);
        }

        return $result;
    }

    /**
     * do pointcut
     *
     * @param array  $pointAdvice the pointcut advice
     * @param object $target      the object of origin
     * @param string $method      the method of object
     * @param array  $args        the params of method
     * @param array  $advice      the advice of pointcut
     * @param array  $advices     the advices of aop
     * @param mixed  $return
     *
     * @return mixed
     */
    private function doPoint(array $pointAdvice, $target, string $method, array $args, array $advice, array $advices, $return = null)
    {
        list($aspectClass, $aspectMethod) = $pointAdvice;

        $rc   = new \ReflectionClass($aspectClass);
        $rm   = $rc->getMethod($aspectMethod);
        $rmps = $rm->getParameters();

        // bind the param of method
        $aspectArgs = [];
        foreach ($rmps as $rmp) {
            $paramType = $rmp->getType();
            if ($paramType === null) {
                $aspectArgs[] = null;
                continue;
            }

            // JoinPoint object
            $type = $paramType->__toString();
            if ($type === JoinPoint::class) {
                $aspectArgs[] = new JoinPoint($target, $method, $args, $return);
                continue;
            }

            // ProceedingJoinPoint object
            if ($type == ProceedingJoinPoint::class) {
                $aspectArgs[] = new ProceedingJoinPoint($target, $method, $args, $advice, $advices, $return);
                continue;
            }
            $aspectArgs[] = null;
        }

        $aspect = App::getBean($aspectClass);

        return $aspect->$aspectMethod(...$aspectArgs);
    }

    /**
     * match aop
     *
     * @param string $beanName    the name of bean
     * @param string $class       class name
     * @param string $method      method
     * @param array  $annotations the annotations of method
     */
    public function match(string $beanName, string $class, string $method, array $annotations)
    {
        foreach ($this->aspects as $aspectClass => $aspect) {
            if (!isset($aspect['point']) || !isset($aspect['advice'])) {
                continue;
            }

            // incloude
            $pointBeanInclude       = $aspect['point']['bean']['include']?? [];
            $pointAnnotationInclude = $aspect['point']['annotation']['include']?? [];
            $pointExecutionInclude  = $aspect['point']['execution']['include']?? [];

            // exclude
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

    /**
     * register aop
     *
     * @param array $aspects
     */
    public function register(array $aspects)
    {
        array_multisort(array_column($aspects, 'order'), SORT_ASC, $aspects);
        $this->aspects = $aspects;
    }

    /**
     * match bean and annotation
     *
     * @param array $pointAry
     * @param array $classAry
     *
     * @return bool
     */
    private function matchBeanAndAnnotation(array $pointAry, array $classAry): bool
    {
        $intersectAry = array_intersect($pointAry, $classAry);
        if (empty($intersectAry)) {
            return false;
        }

        return true;
    }

    /**
     * match execution
     *
     * @param string $class
     * @param string $method
     * @param array  $executions
     *
     * @return bool
     */
    private function matchExecution(string $class, string $method, array $executions): bool
    {
        foreach ($executions as $execution) {
            $executionAry = explode("::", $execution);
            if (count($executionAry) < 2) {
                continue;
            }

            // class
            list($executionClass, $executionMethod) = $executionAry;
            if ($executionClass != $class) {
                continue;
            }

            // method
            $reg = '/^' . $executionMethod . '$/';
            if (preg_match($reg, $method)) {
                return true;
            }
        }

        return false;
    }
}