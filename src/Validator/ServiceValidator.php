<?php

namespace Swoft\Validator;

use Swoft\Bean\Annotation\Bean;
use Swoft\Bean\Annotation\ValidatorFrom;

/**
 * the validator of service
 *
 * @Bean()
 * @uses      ServiceValidator
 * @version   2017年12月10日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class ServiceValidator extends AbstractValidator
{
    /**
     * do validator
     *
     * @param mixed $validators
     * @param array ...$params
     *
     * @return mixed
     */
    public function validate($validators, ...$params)
    {
        list($serviceHandler, $serviceData) = $params;
        $args = $this->getServiceArgs($serviceHandler, $serviceData);

        foreach ($validators as $type => $validator) {
            if ($type != ValidatorFrom::SERVICE) {
                continue;
            }
            $this->validateArg($args, $validator);
        }

        return;
    }


    /**
     * validate arg
     *
     * @param array $args
     * @param array $validator
     */
    public function validateArg(array $args, array $validator)
    {
        foreach ($validator as $name => $info) {
            if (!isset($args[$name])) {
                continue;
            }
            $this->doValidation($args[$name], $info);
        }
    }

    /**
     * get args of called function
     *
     * @param array $serviceHandler
     * @param array $serviceData
     *
     * @return array
     */
    private function getServiceArgs(array $serviceHandler, array $serviceData)
    {
        list($className, $method) = $serviceHandler;
        $rc     = new \ReflectionClass($className);
        $rm     = $rc->getMethod($method);
        $mps    = $rm->getParameters();
        $params = $serviceData['params']??[];

        if (empty($params)) {
            return [];
        }

        $index = 0;
        $args  = [];
        foreach ($mps as $mp) {
            $name = $mp->getName();
            if (!isset($params[$index])) {
                break;
            }
            $args[$name] = $params[$index];
            $index++;
        }

        return $args;
    }
}