<?php

namespace Swoft\Validator;

use Swoft\App;
use Swoft\Bean\BeanFactory;
use Swoft\Exception\ValidatorException;

/**
 * abstract validator
 *
 * @uses      AbstractValidator
 * @version   2017年12月04日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
abstract class AbstractValidator implements ValidatorInterface
{
    /**
     * do validation
     *
     * @param mixed $value validation
     * @param array $info  the info of validator
     *
     * @throws \Swoft\Exception\ValidatorException
     */
    protected function doValidation($value, array $info)
    {
        if (!isset($info['validator']) || !isset($info['params'])) {
            return;
        }

        $validatorBeanName = $info['validator'];
        if (!BeanFactory::hasBean($validatorBeanName)) {
            throw new ValidatorException("the bean of $validatorBeanName is not exist!");
        }

        /* @var \Swoft\Validator\ValidatorInterface $validator */
        $params = $info['params'];
        array_unshift($params, $value);
        $validator = App::getBean($validatorBeanName);
        $validator->validate(...$params);
    }
}