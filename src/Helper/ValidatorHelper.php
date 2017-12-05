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
    private static $numberPattern = '/^\s*[+]?\d+\s*$/';
    private static $integerPattern = '/^\s*[+-]?\d+\s*$/';
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
            return self::validateError("$value is not int", $throws);
        }

        if ($min !== null && $value < $min) {
            return self::validateError("$value is too small (minimum is $min)", $throws);
        }

        if ($max !== null && $value > $max) {
            return self::validateError("$value is too big (maximum is $max)", $throws);
        }

        return $value;
    }

    public static function validateNumber($value, $min = null, $max = null, bool $throws = true)
    {
        if (!is_int($value) || $value < 0) {
            return self::validateError("$value is not number", $throws);
        }

        if ($min !== null && $value < $min) {
            return self::validateError("$value is too small (minimum is $min)", $throws);
        }

        if ($max !== null && $value > $max) {
            return self::validateError("$value is too big (maximum is $max)", $throws);
        }

        return $value;
    }

    /**
     * @param mixed      $value
     * @param float|null $min
     * @param float|null $max
     * @param float|null $default
     * @param bool       $throws
     *
     * @return bool|float
     */
    public static function validateFloat($value, float $min = null, float $max = null, float $default = null, bool $throws = true)
    {
        if (!is_float($value)) {
            return self::validateError("$value is not float", $throws);
        }

        if ($min !== null && $value < $min) {
            return self::validateError("$value is too small (minimum is $min)", $throws);
        }

        if ($max !== null && $value > $max) {
            return self::validateError("$value is too big (maximum is $max)", $throws);
        }

        return $value;
    }

    public static function validateString($value, int $min = null, int $max = null, string $default = null, bool $throws = true)
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

    public static function validateEnumString($value, array $validValues, string $default = null, bool $throws = true)
    {
        $value = self::validateString($value, null, null, $throws);
        if (!in_array($value, $validValues)) {
            return self::validateError("$value is not valid enum!", $throws);
        }

        return $value;
    }

    public static function validateEnumInteger($value, array $validValues, bool $throws = true)
    {
        $value = self::validateInteger($value, null, null, $throws);
        if (!in_array($value, $validValues)) {
            return self::validateError("$value is not valid enum!", $throws);
        }

        return $value;
    }

    public static function validateEnumFloat($value, array $validValues, float $default = null, bool $throws = true)
    {
        $value = self::validateInteger($value, null, null, $throws);
        if (!in_array($value, $validValues)) {
            return self::validateError("$value is not valid enum!", $throws);
        }

        return $value;
    }

    public static function validateEnumNumber($value, array $validValues, bool $throws = true)
    {
        $value = self::validateNumber($value, null, null, $throws);
        if (!in_array($value, $validValues)) {
            return self::validateError("$value is not valid enum!", $throws);
        }

        return $value;
    }

    private function validateError(string $message, bool $throws)
    {
        if ($throws) {
            throw new ValidatorException($message);
        }

        return false;
    }
}