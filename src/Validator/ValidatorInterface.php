<?php

namespace Swoft\Validator;

/**
 * validator interface
 *
 * @uses      ValidatorInterface
 * @version   2017年12月02日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
interface ValidatorInterface
{
    /**
     * do validate
     *
     * @param mixed $value
     * @param array ...$params
     *
     * @return mixed
     */
    public function validate($value, ...$params);
}
