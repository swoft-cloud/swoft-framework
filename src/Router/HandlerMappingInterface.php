<?php

namespace Swoft\Router;

/**
 * handler mapping interface
 *
 * @uses      HandlerMappingInterface
 * @version   2017年11月23日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
interface HandlerMappingInterface
{
    /**
     * the handler of controller
     *
     * @param array ...$params
     *
     * @return array
     */
    public function getHandler(...$params);
}