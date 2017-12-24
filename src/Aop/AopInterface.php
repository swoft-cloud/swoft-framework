<?php

namespace Swoft\Aop;

/**
 * the interface of aop
 *
 * @uses      AopInterface
 * @version   2017年12月24日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
interface AopInterface
{
    public function execute($target, string $method, array $params);
    public function register(array $aspects);
}