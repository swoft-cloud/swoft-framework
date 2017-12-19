<?php

namespace Swoft\Pool;

/**
 * the interface of selector
 *
 * @uses      SelectorInterface
 * @version   2017年12月16日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
interface SelectorInterface
{
    /**
     * @param string $type
     *
     * @return mixed
     */
    public function select(string $type);
}