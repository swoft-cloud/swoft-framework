<?php

namespace Swoft\Validator;

use Swoft\App;
use Swoft\Bean\Annotation\Bean;
use Swoft\Bean\Annotation\ValidatorFrom;
use Swoft\Bean\BeanFactory;

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
        if (!is_array($validators)) {
            return false;
        }

        /**
         * @var \Swoft\Web\Request $request
         * @var array              $matches
         */
        list($request, $matches) = $params;

        $get  = $request->getQueryParams();
        $post = $request->getParsedBody();
        foreach ($validators as $type => $validatorAry) {
            $this->validateField($get, $post, $matches, $type, $validatorAry);
        }

        return true;
    }

    private function validateField($get, $post, $matches, $type, $validatorAry)
    {
        foreach ($validatorAry as $name => $info) {
            if ($type == ValidatorFrom::GET) {
                if (!isset($get[$name])) {
                    continue;
                }
                $this->doValidation($get[$name], $info);

                continue;
            }
            if ($type == ValidatorFrom::POST) {
                if (!isset($post[$name])) {
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

            if ($type == ValidatorFrom::QUERY && isset($get[$name])) {
                if (!isset($get[$name])) {
                    continue;
                }
                $this->doValidation($get[$name], $info);

                continue;
            }

            if ($type == ValidatorFrom::QUERY && isset($post[$name])) {
                if (!isset($post[$name])) {
                    continue;
                }
                $this->doValidation($post[$name], $info);

                continue;
            }
            if ($type == ValidatorFrom::QUERY && isset($matches[$name])) {
                if (!isset($matches[$name])) {
                    continue;
                }
                $this->doValidation($matches[$name], $info);

                continue;
            }
        }
    }

}