<?php

namespace Swoft\Testing\Aop;

use Swoft\Bean\Annotation\Bean;

/**
 * @Bean()
 *
 * @uses      RegBean
 * @version   2017年12月27日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class RegBean
{
    public function regMethod()
    {
        return 'regMethod';
    }

    public function regMethod2()
    {
        return 'regMethod2';
    }

    public function methodParams($a, $b)
    {
        return 'methodParams-'.$a."-".$b;
    }
}
