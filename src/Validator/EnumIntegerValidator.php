<?php

namespace Swoft\Validator;

use Swoft\Bean\Annotation\Bean;
use Swoft\Helper\ValidatorHelper;

/**
 * enum integer validator
 *
 * @Bean()
 * @uses      EnumIntegerValidator
 * @version   2017年12月04日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class EnumIntegerValidator implements ValidatorInterface
{
    /**
     * @param mixed $value
     * @param array ...$params
     *
     * @return mixed
     */
    public function validate($value, ...$params)
    {
        list($value, $validValues, $default) = $params;

        return ValidatorHelper::validateEnumInteger($value, $validValues, $default);
    }
}