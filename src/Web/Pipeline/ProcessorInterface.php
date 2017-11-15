<?php

namespace Swoft\Web\Pipeline;


/**
 * @uses      ProcessorInterface
 * @version   2017年11月15日
 * @author    huangzhhui <huangzhwork@gmail.com>
 * @copyright Copyright 2010-2017 Swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
interface ProcessorInterface
{

    /**
     * @param mixed $payload
     * @return mixed
     */
    public function process($payload);

}