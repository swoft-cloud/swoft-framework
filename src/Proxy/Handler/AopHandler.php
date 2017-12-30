<?php

namespace Swoft\Proxy\Handler;

use Swoft\Aop\Aop;
use Swoft\App;
use Swoft\Base\ApplicationContext;

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

    /**
     * ProceedingJoinPoint
     * JoinPoint
     *
     * @param $method
     * @param $parameters
     *
     * @return mixed
     */
    public function invoke($method, $parameters)
    {
        /* @var Aop $aop*/
        $aop = App::getBean(Aop::class);
        return $aop->execute($this->target, $method, $parameters);
    }
}
