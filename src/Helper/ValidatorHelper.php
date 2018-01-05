<?php

namespace Swoft\Helper;

use Swoft\Exception\ValidatorException;

/**
 * the tool of validator
 *
 * @uses      ValidatorHelper
 * @version   2017年12月02日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class ValidatorHelper
{
    /**
     * number pattern
     *
     * @var string
     */
    private static $numberPattern = '/^\s*[+]?\d+\s*$/';

    /**
     * integer pattern
     *
     * @var string
     */
    private static $integerPattern = '/^\s*[+-]?\d+\s*$/';

    /**
     * float pattern
     *
     * @var string
     */
    private static $floatPattern = '/^(-?\d+)(\.\d+)+$/';

    /**
     * the validator of integer
     *
     * @param mixed    $value
     * @param int|null $min
     * @param int|null $max
     * @param bool     $throws
     *
     * @throws ValidatorException;
     * @return mixed
     */
    public static function validateInteger($value, $min = null, $max = null, bool $throws = true)
    {
        if (!preg_match(self::$integerPattern, "$value")) {
            return self::validateError("$value is not integer", $throws);
        }

        $value = (int)$value;
        if ($min !== null && $value < $min) {
            return self::validateError("$value is too small (minimum is $min)", $throws);
        }

        if ($max !== null && $value > $max) {
            return self::validateError("$value is too big (maximum is $max)", $throws);
        }

        return $value;
    }

    /**
     * the validator of number
     *
     * @param mixed    $value
     * @param int|null $min
     * @param int|null $max
     * @param bool     $throws
     *
     * @throws ValidatorException;
     * @return mixed
     */
    public static function validateNumber($value, $min = null, $max = null, bool $throws = true)
    {
        if (!preg_match(self::$numberPattern, "$value")) {
            return self::validateError("$value is not number", $throws);
        }

        $value = (int)$value;
        if ($min !== null && $value < $min) {
            return self::validateError("$value is too small (minimum is $min)", $throws);
        }

        if ($max !== null && $value > $max) {
            return self::validateError("$value is too big (maximum is $max)", $throws);
        }

        return $value;
    }

    /**
     * the validator of float
     *
     * @param mixed      $value
     * @param float|null $min
     * @param float|null $max
     * @param bool       $throws
     *
     * @throws ValidatorException;
     * @return mixed
     */
    public static function validateFloat($value, float $min = null, float $max = null, bool $throws = true)
    {
        if (!preg_match(self::$floatPattern, "$value")) {
            return self::validateError("$value is not float", $throws);
        }

        $value = (float)$value;
        if ($min !== null && $value < $min) {
            return self::validateError("$value is too small (minimum is $min)", $throws);
        }

        if ($max !== null && $value > $max) {
            return self::validateError("$value is too big (maximum is $max)", $throws);
        }

        return $value;
    }

    /**
     * the validator of string
     *
     * @param mixed    $value
     * @param int|null $min
     * @param int|null $max
     * @param bool     $throws
     *
     * @throws ValidatorException;
     * @return mixed
     */
    public static function validateString($value, int $min = null, int $max = null, bool $throws = true)
    {
        if (!is_string($value)) {
            return self::validateError("$value is not string", $throws);
        }
        $length = mb_strlen($value);
        if ($min !== null && $length < $min) {
            return self::validateError("$value is too small (minimum is $min)", $throws);
        }

        if ($max !== null && $length > $max) {
            return self::validateError("$value is too big (maximum is $max)", $throws);
        }

        return $value;
    }

    /**
     * the validator of enum
     *
     * @param mixed $value
     * @param array $validValues
     * @param bool  $throws
     *
     * @return bool
     */
    public static function validateEnum($value, array $validValues, bool $throws = true)
    {
        if (!in_array($value, $validValues)) {
            return self::validateError("$value is not valid enum!", $throws);
        }

        return $value;
    }

    /**
     * do error
     *
     * @param string $message
     * @param bool   $throws
     *
     * @return bool
     * @throws \Swoft\Exception\ValidatorException
     */
    private static function validateError(string $message, bool $throws)
    {
        if ($throws) {
            throw new ValidatorException($message);
        }

        return false;
    }
}
