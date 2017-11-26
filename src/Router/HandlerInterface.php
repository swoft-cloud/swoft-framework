<?php

namespace Swoft\Router;

/**
 * handler interface
 *
 * @uses      HandlerInterface
 * @version   2017年11月23日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
interface HandlerInterface
{
    public function run();
}