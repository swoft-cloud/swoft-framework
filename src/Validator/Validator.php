<?php

namespace Swoft\Validator;

use Swoft\Bean\Annotation\Bean;

/**
 * validator of swoft
 *
 * @Bean()
 * @uses      Validator
 * @version   2017年12月03日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class Validator implements ValidatorInterface
{
    /**
     * do validate
     *
     * @param mixed $validators
     * @param array ...$params
     * @return mixed
     */
    public function validate($validators, ...$params)
    {
        if(!is_array($validators)){
            return false;
        }

        /* @var \Swoft\Base\Request $request*/
        list($request) = $params;
    }
}