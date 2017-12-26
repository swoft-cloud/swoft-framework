<?php

namespace Swoft\Testing\Aop;

use Swoft\Bean\Annotation\Bean;

/**
 *
 * @Bean()
 * @uses      AopBean
 * @version   2017年12月26日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class AopBean
{
    public function doAop()
    {
        return "do aop";
    }
}