<?php

namespace Swoft\Validator;

use Swoft\Bean\Annotation\Bean;
use Swoft\Bean\Annotation\ValidatorFrom;
use Swoft\Web\Request;

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
class HttpValidator extends AbstractValidator
{
    /**
     * do validate
     *
     * @param mixed $validators
     * @param array ...$params
     *
     * @return mixed
     */
    public function validate($validators, ...$params)
    {
        /**
         * @var Request $request
         * @var array   $matches
         */
        list($request, $matches) = $params;

        if (!is_array($validators)) {
            return $request;
        }

        foreach ($validators as $type => $validatorAry) {
            $request = $this->validateField($request, $matches, $type, $validatorAry);
        }

        return $request;
    }

    /**
     * validate
     *
     * @param Request $request
     * @param array   $matches
     * @param string  $type
     * @param array   $validatorAry
     *
     * @return mixed
     */
    private function validateField($request, array $matches, string $type, array $validatorAry)
    {
        $get  = $request->getQueryParams();
        $post = $request->getParsedBody();
        foreach ($validatorAry as $name => $info) {
            $default = array_pop($info['params']);
            if ($type == ValidatorFrom::GET) {
                if (!isset($get[$name])) {
                    $request = $request->addQueryParam($name, $default);
                    continue;
                }
                $this->doValidation($get[$name], $info);

                continue;
            }
            if ($type == ValidatorFrom::POST && is_array($post)) {
                if (!isset($post[$name])) {
                    $request = $request->addParserBody($name, $default);
                    continue;
                }
                $this->doValidation($post[$name], $info);
                continue;
            }
            if ($type == ValidatorFrom::PATH) {
                if (!isset($matches[$name])) {
                    continue;
                }
                $this->doValidation($matches[$name], $info);
                continue;
            }
        }

        return $request;
    }
}
