<?php

namespace Swoft\Test\Base\Pipeline;

use Swoft\Web\Pipeline\AbstractProcessor;


/**
 * @uses      TestProcessor
 * @version   2017年11月15日
 * @author    huangzhhui <huangzhwork@gmail.com>
 * @copyright Copyright 2010-2017 Swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class TestProcessor extends AbstractProcessor
{

    /**
     * @param mixed $payload
     * @return mixed
     */
    public function process($payload)
    {
        return $payload + 1;
    }
}