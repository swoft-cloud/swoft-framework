<?php

namespace Swoft\Validator;

use Swoft\Bean\Annotation\Bean;
use Swoft\Helper\ValidatorHelper;

/**
 * enum string validator
 *
 * @Bean()
 * @uses      EnumStringValidator
 * @version   2017年12月04日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class EnumStringValidator implements ValidatorInterface
{
    /**
     * @param mixed $value
     * @param array ...$params
     *
     * @return mixed
     */
    public function validate($value, ...$params)
    {
        list($value, $validValues) = $params;

        return ValidatorHelper::validateEnumString($value, $validValues);
    }
}