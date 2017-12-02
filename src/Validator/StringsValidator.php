<?php

namespace Swoft\Validator;

use Swoft\Helper\ValidatorHelper;

/**
 *
 *
 * @uses      StringsValidator
 * @version   2017年12月02日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class StringsValidator implements ValidatorInterface
{
    /**
     * @param mixed $value
     * @param array ...$params
     *
     * @return mixed
     */
    public function validate($value, ...$params)
    {
        list($min, $max, $default) = $params;

        return ValidatorHelper::validateString($value, $min, $max, $default);
    }
}