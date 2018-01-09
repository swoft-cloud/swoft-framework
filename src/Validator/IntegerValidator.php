<?php

namespace Swoft\Validator;

use Swoft\Bean\Annotation\Bean;
use Swoft\Helper\ValidatorHelper;

/**
 * integer validator
 *
 * @Bean()
 * @uses      IntegerValidator
 * @version   2017年12月04日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class IntegerValidator
{
    /**
     * @param mixed $value
     * @param array ...$params
     *
     * @return mixed
     */
    public function validate($value, ...$params)
    {
        list($min, $max) = $params;

        return ValidatorHelper::validateInteger($value, $min, $max);
    }
}
