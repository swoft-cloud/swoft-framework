<?php

namespace Swoft\Pipeline;

/**
 * @uses      AbstractProcessor
 * @version   2017年11月15日
 * @author    huangzhhui <huangzhwork@gmail.com>
 * @copyright Copyright 2010-2017 Swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
abstract class AbstractProcessor implements ProcessorInterface
{

    /**
     * @var array
     */
    public $stages = [];

    /**
     * FingersCrossedProcessor constructor.
     *
     * @param array $stages
     */
    public function __construct(array $stages = [])
    {
        $stages && $this->stages = $stages;
    }
}
